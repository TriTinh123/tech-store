<?php

namespace App\Services;

use App\Jobs\AnalyzeLoginBehavior;
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
 * Records audit logs on login, aggregates features by userId/IP,
 * sends to AI for analysis (only when thresholds exceeded), handles results.
 *
 * Architecture:
 *   Does NOT send raw logs — only sends aggregated feature numbers.
 */
class AuditLogService
{
    private const AI_URL       = 'http://127.0.0.1:5001/audit-log/analyze';
    private const TIME_WINDOW  = 10; // minutes — aggregation window

    // Rule-based thresholds — no AI call needed for clear-cut cases
    private const FAIL_ATTACK  = 10; // ≥10 failures/window → attack
    private const FAIL_SUSPECT = 3;  // ≥3 failures/window → send to AI

    /**
     * Quick check: does this login attempt show suspicious signals?
     * Used to decide whether OTP (F2) is required. Read-only, no DB writes.
     */
    public function isSuspicious(Request $request, User $user): bool
    {
        // ── Cart value threshold: ≥ 2,000,000 VND always forces OTP ─────────
        $cartValue = collect(session('cart', []))
            ->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? $item['quantity'] ?? 1));
        if ($cartValue >= 2_000_000) {
            return true;
        }

        // ── Demo Mode: override all signals with simulated values ────────────
        if ($request->input('demo_mode') === '1') {
            $failedCount = (int) $request->input('demo_failed_attempts', 0);
            $ipCount     = (int) $request->input('demo_ip_count', 0) > 2 ? 3 : 1;
            $isNewIp     = $request->input('demo_new_ip')     === '1';
            $isNewDevice = $request->input('demo_new_device') === '1';
            $geoChanged  = $request->input('demo_geo_changed') === '1';

            return $failedCount >= self::FAIL_SUSPECT
                || $ipCount > 2
                || ($isNewIp && $isNewDevice)
                || $geoChanged;
        }

        $win = now()->subMinutes(self::TIME_WINDOW);

        $base = LoginAttempt::where('user_id', $user->id)
            ->where('created_at', '>=', $win);

        $failedCount = (clone $base)->where('password_ok', false)->count();
        $ipCount     = (clone $base)->distinct('ip_address')->count('ip_address');
        $deviceCount = (clone $base)->distinct('user_agent')->count('user_agent');

        // Is the current IP or device fingerprint unknown?
        $knownIps     = $user->known_ips     ?? [];
        $knownDevices = $user->known_devices ?? [];
        $fp           = $this->deviceFingerprint($request);
        $isNewIp      = ! in_array($request->ip(), $knownIps);
        $isNewDevice  = ! in_array($fp, $knownDevices);

        return $failedCount >= self::FAIL_SUSPECT
            || $ipCount > 2
            || $deviceCount > 2
            || ($isNewIp && $isNewDevice);  // unknown device + unknown IP simultaneously
    }

    /**
     * Record a login attempt into audit_logs, then decide if AI analysis is needed.
     */
    public function record(Request $request, ?User $user, bool $passwordOk): AuditLog
    {
        $ip  = $request->ip();
        $fp  = $this->deviceFingerprint($request);
        $now = now();
        $win = $now->copy()->subMinutes(self::TIME_WINDOW);

        // ── Aggregate features within TIME_WINDOW ───────────────────────────
        $base = LoginAttempt::where(function ($q) use ($user, $ip) {
            if ($user) $q->where('user_id', $user->id);
            else       $q->where('ip_address', $ip);
        })->where('created_at', '>=', $win);

        $failedCount  = (clone $base)->where('password_ok', false)->count();
        $ipCount      = (clone $base)->distinct('ip_address')->count('ip_address');
        $deviceCount  = (clone $base)->distinct('user_agent')->count('user_agent');

        // Geo: get country from user's most recent LoginAttempt (if any)
        $lastCountry     = null;
        $geoChanged      = false;
        if ($user) {
            $prevLogin   = LoginAttempt::where('user_id', $user->id)
                ->whereNotNull('geo_country')
                ->latest()->first();
            $lastCountry = $prevLogin?->geo_country;
            // Compare with current geo
            $geoChanged  = $lastCountry && $lastCountry !== 'VN' && $lastCountry !== null;
        }

        $features = [
            'failed_attempt'  => $failedCount,
            'ip_count'        => $ipCount,
            'device_count'    => $deviceCount,
            'time_window_min' => self::TIME_WINDOW,
            'geo_changed'     => $geoChanged ? 1 : 0,
        ];

        // ── Rule fallback (no AI needed) ──────────────────────────────────────
        if ($failedCount >= self::FAIL_ATTACK) {
            return $this->save($request, $user, $passwordOk, $fp, $features, [
                'ai_result'      => 'attack',
                'ai_risk_score'  => 100,
                'account_locked' => true,
                'email_sent'     => false,
                'event'          => 'attack',
            ]);
        }

        // ── Only dispatch async AI job when there are suspicious signals ──────────
        $needsAi = ! $passwordOk
                && ($failedCount >= self::FAIL_SUSPECT || $ipCount > 1 || $deviceCount > 1 || $geoChanged);

        $event = $passwordOk ? 'login_success' : 'login_attempt';

        $log = $this->save($request, $user, $passwordOk, $fp, $features, [
            'ai_result'      => null,
            'ai_risk_score'  => null,
            'account_locked' => false,
            'email_sent'     => false,
            'event'          => $event,
        ]);

        // Dispatch async — does not block the login response
        if ($needsAi) {
            AnalyzeLoginBehavior::dispatch($features, (int) $log->getKey(), $user?->id);
        }

        return $log;
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function save(Request $request, ?User $user, bool $passwordOk, string $fp, array $features, array $extra): AuditLog
    {
        /** @var AuditLog $log */
        $log = new AuditLog(array_merge([  // @phpstan-ignore-line
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
        $log->save();
        return $log;
    }

    /**
     * Send aggregated features to Python AI endpoint /audit-log/analyze.
     * Does NOT send raw logs — only 5 aggregated numbers.
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
            // Lock account
            $user->update(['is_blocked' => true]);
            $log->update(['account_locked' => true]);
            Log::warning("AuditLog: account #{$user->id} LOCKED — AI detected attack.");
        }

        if (in_array($verdict, ['suspicious', 'attack']) && $user) {
            // Send security alert email
            try {
                $subject = $verdict === 'attack'
                    ? '🚨 Account under attack — temporarily locked'
                    : '⚠️ Suspicious login activity detected';
                $message = $verdict === 'attack'
                    ? 'AI detected a brute-force attack. Your account has been temporarily locked.'
                    : 'We detected unusual login behaviour. Please change your password immediately if this was not you.';

                Mail::raw(
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
