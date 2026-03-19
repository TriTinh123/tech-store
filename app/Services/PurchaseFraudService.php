<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * PurchaseFraudService — Detect Unusual Purchase (Fraud Detection)
 * ================================================================
 *
 * Evaluates 5 independent risk signals and decides whether an OTP
 * confirmation is required before the order can be placed.
 *
 * Risk signals (additive score):
 *  1. High order amount  (≥ 5 000 000 ₫ → +50 pts)
 *  2. Medium amount      (≥ 2 000 000 ₫ → +25 pts)
 *  3. Unknown device     (fingerprint not in user.known_devices → +30 pts)
 *  4. Unknown IP address (IP not in user.known_ips → +15 pts)
 *  5. Recent login anomaly (last successful session was AI-flagged → +35 pts)
 *  6. Brute-force history (≥ 3 failed logins last hour → +20 pts)
 *
 * Requires OTP when total score ≥ 40  OR  order ≥ HIGH_AMOUNT.
 */
class PurchaseFraudService
{
    /** Absolute threshold: always flag regardless of other signals */
    const HIGH_AMOUNT   = 5_000_000;   // 5 million VND

    /** Conditional threshold: flag only when combined with risk signals */
    const MEDIUM_AMOUNT = 2_000_000;   // 2 million VND

    /**
     * Assess a purchase for fraud risk signals.
     *
     * @param  Request $request  Current HTTP request (for IP + device FP)
     * @param  User    $user     Authenticated user
     * @param  float   $total    Cart total in VND
     * @return array {
     *   risk_level   : 'low'|'medium'|'high'|'critical'
     *   risk_numeric : int (0-100)
     *   requires_otp : bool
     *   reasons      : string[]
     *   total        : float
     *   is_new_device: bool
     * }
     */
    public function assess(Request $request, User $user, float $total): array
    {
        $reasons   = [];
        $riskScore = 0;

        // ── Signal 1: High order amount ────────────────────────────────────
        if ($total >= self::HIGH_AMOUNT) {
            $reasons[] = 'Very high-value order ('
                . number_format($total, 0, ',', '.') . ' ₫ ≥ 5.000.000 ₫)';
            $riskScore += 50;
        } elseif ($total >= self::MEDIUM_AMOUNT) {
            $reasons[] = 'High-value order ('
                . number_format($total, 0, ',', '.') . ' ₫ ≥ 2.000.000 ₫)';
            $riskScore += 25;
        }

        // ── Signal 2: Unknown device fingerprint ───────────────────────────
        $fp          = $this->deviceFingerprint($request);
        $known       = $user->known_devices ?? [];
        $isNewDevice = ! in_array($fp, $known);

        if ($isNewDevice) {
            $reasons[] = 'Device has never placed an order from this account';
            $riskScore += 30;
        }

        // ── Signal 3: Unknown IP address ───────────────────────────────────
        $knownIps = $user->known_ips ?? [];
        $ip       = $request->ip();

        if (! in_array($ip, $knownIps)) {
            $reasons[] = 'Unrecognized IP address (' . $ip . ')';
            $riskScore += 15;
        }

        // ── Signal 4: Most recent login was AI-flagged as anomaly ──────────
        $lastLogin = LoginAttempt::where('user_id', $user->id)
            ->where('success', true)
            ->latest()
            ->first();

        if ($lastLogin && $lastLogin->is_anomaly) {
            $level     = strtoupper($lastLogin->risk_level ?? 'UNKNOWN');
            $reasons[] = 'Current login session flagged as anomalous by AI (level ' . $level . ')';
            $riskScore += 35;
        }

        // ── Signal 5: Brute-force / failed login history ───────────────────
        $recentFails = LoginAttempt::where('user_id', $user->id)
            ->where('password_ok', false)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentFails >= 3) {
            $reasons[] = "Detected {$recentFails} failed login attempt(s) in the past hour";
            $riskScore += 20;
        }

        // ── Derive level + decision ────────────────────────────────────────
        $riskNumeric = min(100, $riskScore);
        $riskLevel   = $this->scoreToLevel($riskNumeric);

        // Require OTP when score ≥ 40 (1+ combined signals) OR outright large order
        $requiresOtp = $riskNumeric >= 40 || $total >= self::HIGH_AMOUNT;

        return [
            'risk_level'    => $riskLevel,
            'risk_numeric'  => $riskNumeric,
            'requires_otp'  => $requiresOtp,
            'reasons'       => $reasons,
            'total'         => $total,
            'is_new_device' => $isNewDevice,
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function deviceFingerprint(Request $request): string
    {
        return md5(
            $request->userAgent() .
            session('auth.screen_w', '') .
            session('auth.screen_h', '')
        );
    }

    private function scoreToLevel(int $score): string
    {
        if ($score >= 70) return 'critical';
        if ($score >= 50) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }
}
