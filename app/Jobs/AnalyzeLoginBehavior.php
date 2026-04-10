<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Async job: send aggregated login behaviour features to AI engine,
 * then act on the result (warn / lock account).
 *
 * Dispatched by AuditLogService when suspicious signals are detected.
 * Runs on the 'audit' queue so it doesn't block login requests.
 */
class AnalyzeLoginBehavior implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int    $tries   = 2;
    public int    $timeout = 8;

    public function __construct(
        private readonly array    $features,
        private readonly int      $auditLogId,
        private readonly ?int     $userId,
    ) {
        $this->onQueue('audit');
    }

    public function handle(): void
    {
        $log  = AuditLog::find($this->auditLogId);
        $user = $this->userId ? User::find($this->userId) : null;

        // ── Rule fallback — no AI needed for obvious cases ────────────────
        $fails = $this->features['failed_attempt'] ?? 0;

        if ($fails >= 10) {
            $this->applyVerdict('attack', 100, $log, $user);
            return;
        }

        // ── Call AI engine ─────────────────────────────────────────────────
        try {
            $response = Http::timeout(6)->post('http://127.0.0.1:5001/audit-log/analyze', [
                'failed_attempt'  => $this->features['failed_attempt'],
                'ip_count'        => $this->features['ip_count'],
                'device_count'    => $this->features['device_count'],
                'time_window_min' => $this->features['time_window_min'],
                'geo_changed'     => $this->features['geo_changed'],
                'user_id'         => $this->userId ?? 0,
            ]);

            if ($response->successful()) {
                $result  = $response->json();
                $verdict = $result['result'] ?? 'normal';

                // Safety guard: AI may return 'attack' aggressively on few fails.
                // Only allow account lock (attack) when rule-based threshold is met (≥10).
                // Below that, downgrade to 'suspicious' — warn but never lock.
                if ($verdict === 'attack' && $fails < 10) {
                    $verdict = 'suspicious';
                }

                $this->applyVerdict($verdict, $result['risk_score'] ?? 0, $log, $user);
                return;
            }

            Log::warning('AnalyzeLoginBehavior: AI returned HTTP ' . $response->status());
        } catch (\Throwable $e) {
            Log::warning('AnalyzeLoginBehavior: AI unreachable — ' . $e->getMessage());
        }

        // ── Fallback: rule-based decision if AI is offline ─────────────────
        $verdict = 'normal';
        if ($fails >= 5 || ($this->features['ip_count'] ?? 0) > 2) {
            $verdict = 'suspicious';
        }
        $this->applyVerdict($verdict, 0, $log, $user);
    }

    // ─── Private ──────────────────────────────────────────────────────────

    private function applyVerdict(string $verdict, int|float $riskScore, ?AuditLog $log, ?User $user): void
    {
        $log?->update([
            'ai_result'      => $verdict,
            'ai_risk_score'  => $riskScore,
            'account_locked' => $verdict === 'attack',
        ]);

        if ($verdict === 'attack' && $user) {
            $user->update(['is_blocked' => true]);
            Log::warning("AnalyzeLoginBehavior: account #{$user->id} LOCKED — brute-force detected.");
        }

        if (in_array($verdict, ['suspicious', 'attack']) && $user) {
            $this->sendAlert($verdict, $riskScore, $user, $log);
        }
    }

    private function sendAlert(string $verdict, int|float $riskScore, User $user, ?AuditLog $log): void
    {
        try {
            $subject = $verdict === 'attack'
                ? '🚨 Account locked — brute-force attack detected'
                : '⚠️  Suspicious login activity on your account';

            $body = $verdict === 'attack'
                ? "Our AI detected a brute-force attack on your account and has locked it.\nRisk score: {$riskScore}/100\n\nContact support if this was you."
                : "We detected unusual login behaviour on your account.\nRisk score: {$riskScore}/100\n\nPlease change your password immediately if this was not you.";

            Mail::raw($body, fn ($m) => $m->to($user->email)->subject($subject));
            $log?->update(['email_sent' => true]);
        } catch (\Throwable $e) {
            Log::warning('AnalyzeLoginBehavior: alert email failed — ' . $e->getMessage());
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('AnalyzeLoginBehavior job failed: ' . $e->getMessage());
    }
}
