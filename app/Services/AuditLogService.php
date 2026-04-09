<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * AuditLogService
 * ===============
 * Ghi audit log khi đăng nhập, gom nhóm feature theo userId/IP,
 * gửi AI phân tích (chỉ khi vượt threshold), xử lý kết quả.
 *
 * Kiến trúc tối ưu token:
 *   KHÔNG gửi raw log → chỉ gửi feature đã gom nhóm (số tổng hợp).
 */
class AuditLogService
{
    private const AI_URL       = 'http://127.0.0.1:5001/audit-log/analyze';
    private const TIME_WINDOW  = 10; // phút — cửa sổ gom nhóm

    // Rule-based threshold → không cần gọi AI (tiết kiệm token)
    private const FAIL_ATTACK  = 10; // ≥10 fail/window → attack ngay
    private const FAIL_SUSPECT = 3;  // ≥3 fail/window → đưa vào AI

    /**
     * Ghi một lần thử đăng nhập vào audit_logs, sau đó quyết định có cần AI không.
     */
    public function record(Request $request, ?User $user, bool $passwordOk): AuditLog
    {
        $ip  = $request->ip();
        $fp  = $this->deviceFingerprint($request);
        $now = now();
        $win = $now->copy()->subMinutes(self::TIME_WINDOW);

        // ── Gom feature trong TIME_WINDOW ──────────────────────────────────
        $base = LoginAttempt::where(function ($q) use ($user, $ip) {
            if ($user) $q->where('user_id', $user->id);
            else       $q->where('ip_address', $ip);
        })->where('created_at', '>=', $win);

        $failedCount  = (clone $base)->where('password_ok', false)->count();
        $ipCount      = (clone $base)->distinct('ip_address')->count('ip_address');
        $deviceCount  = (clone $base)->distinct('user_agent')->count('user_agent');

        // Geo: lấy country từ LoginAttempt cuối của user (nếu có)
        $lastCountry     = null;
        $geoChanged      = false;
        if ($user) {
            $prevLogin   = LoginAttempt::where('user_id', $user->id)
                ->whereNotNull('geo_country')
                ->latest()->first();
            $lastCountry = $prevLogin?->geo_country;
            // So sánh với geo hiện tại (simple: dùng thuật toán AiRiskService nếu cần)
            $geoChanged  = $lastCountry && $lastCountry !== 'VN' && $lastCountry !== null;
        }

        $features = [
            'failed_attempt'  => $failedCount,
            'ip_count'        => $ipCount,
            'device_count'    => $deviceCount,
            'time_window_min' => self::TIME_WINDOW,
            'geo_changed'     => $geoChanged ? 1 : 0,
        ];

        // ── Rule fallback (không cần AI) ───────────────────────────────────
        if ($failedCount >= self::FAIL_ATTACK) {
            return $this->save($request, $user, $passwordOk, $fp, $features, [
                'ai_result'      => 'attack',
                'ai_risk_score'  => 100,
                'account_locked' => true,
                'email_sent'     => false,
                'event'          => 'attack',
            ]);
        }

        // ── Chỉ gọi AI khi có dấu hiệu đáng ngờ (≥ FAIL_SUSPECT lần sai HOẶC IP/device đổi) ──
        $needsAi = ! $passwordOk
                && ($failedCount >= self::FAIL_SUSPECT || $ipCount > 1 || $deviceCount > 1 || $geoChanged);

        $aiResult = null;
        if ($needsAi) {
            $aiResult = $this->callAi($features, $user);
        }

        $event = $passwordOk ? 'login_success' : 'login_attempt';
        if ($aiResult) {
            $event = match ($aiResult['result'] ?? 'normal') {
                'attack'     => 'attack',
                'suspicious' => 'suspicious',
                default      => $event,
            };
        }

        $log = $this->save($request, $user, $passwordOk, $fp, $features, [
            'ai_result'      => $aiResult['result']     ?? null,
            'ai_risk_score'  => $aiResult['risk_score'] ?? null,
            'account_locked' => ($aiResult['result'] ?? '') === 'attack',
            'email_sent'     => false,
            'event'          => $event,
        ]);

        // ── Xử lý hậu kết quả ─────────────────────────────────────────────
        if ($aiResult) {
            $this->handleAiResult($aiResult, $user, $log);
        }

        return $log;
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function save(Request $request, ?User $user, bool $passwordOk, string $fp, array $features, array $extra): AuditLog
    {
        return AuditLog::create(array_merge([
            'user_id'            => $user?->id,
            'email'              => $user?->email ?? $request->input('email'),
            'ip_address'         => $request->ip(),
            'device_fingerprint' => $fp,
            'password_ok'        => $passwordOk,
            'failed_attempts'    => $features['failed_attempt'],
            'ip_count'           => $features['ip_count'],
            'device_count'       => $features['device_count'],
            'geo_country'        => null,
            'geo_changed'        => (bool) $features['geo_changed'],
            'raw_features'       => $features,
        ], $extra));
    }

    /**
     * Gửi feature đã gom nhóm lên Python AI endpoint /audit-log/analyze.
     * KHÔNG gửi raw log — chỉ gửi 5 số tổng hợp → tiết kiệm token tối đa.
     */
    private function callAi(array $features, ?User $user): ?array
    {
        try {
            $response = Http::timeout(4)->post(self::AI_URL, [
                'failed_attempt'  => $features['failed_attempt'],
                'ip_count'        => $features['ip_count'],
                'device_count'    => $features['device_count'],
                'time_window_min' => $features['time_window_min'],
                'geo_changed'     => $features['geo_changed'],
                'user_id'         => $user?->id ?? 0,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('AuditLogService: AI endpoint unreachable — ' . $e->getMessage());
        }
        return null;
    }

    private function handleAiResult(array $result, ?User $user, AuditLog $log): void
    {
        $verdict = $result['result'] ?? 'normal';

        if ($verdict === 'attack' && $user) {
            // Khóa tài khoản
            $user->update(['is_blocked' => true]);
            $log->update(['account_locked' => true]);
            Log::warning("AuditLog: account #{$user->id} LOCKED — AI detected attack.");
        }

        if (in_array($verdict, ['suspicious', 'attack']) && $user) {
            // Gửi email cảnh báo
            try {
                $subject = $verdict === 'attack'
                    ? '🚨 Tài khoản bị tấn công — đã bị khóa'
                    : '⚠️ Phát hiện hoạt động đáng ngờ';
                $message = $verdict === 'attack'
                    ? 'AI phát hiện tấn công brute-force. Tài khoản đã bị khóa tạm thời.'
                    : 'Chúng tôi phát hiện hành vi đăng nhập bất thường. Hãy đổi mật khẩu ngay nếu không phải bạn.';

                \Illuminate\Support\Facades\Mail::raw(
                    "[{$subject}]\n\n{$message}\n\nRisk score: " . ($result['risk_score'] ?? 0),
                    function ($m) use ($user, $subject) {
                        $m->to($user->email)->subject($subject);
                    }
                );
                $log->update(['email_sent' => true]);
            } catch (\Throwable $e) {
                Log::warning('AuditLog email failed: ' . $e->getMessage());
            }
        }
    }

    private function deviceFingerprint(Request $request): string
    {
        return substr(md5($request->userAgent() . $request->ip()), 0, 16);
    }
}
