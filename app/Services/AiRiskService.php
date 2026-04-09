<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiRiskService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.python_ai.url', 'http://127.0.0.1:5001');
    }

    /**
        * Call the Python FastAPI risk engine and return a structured result.
     * Falls back to a safe LOW-risk result if the AI service is unavailable.
     */
    public function assess(Request $request, User $user): array
    {
        // ── Trusted device/IP shortcut ────────────────────────────────────────────
        // If the user is logging in from a known IP with a known device,
        // OTP has already confirmed identity → skip AI scoring, return LOW risk.
        // This mirrors how Google/Apple treat recognised devices.
        $isNewIp     = $this->isNewIp($request->ip(), $user);
        $isNewDevice = $isNewIp === 0 ? 0 : $this->isNewDevice($request, $user);

        if ($isNewIp === 0 && $isNewDevice === 0) {
            // Even on trusted devices, too many recent failures = account under attack → 3FA
            $failures = $this->recentFailures($user);
            if ($failures >= 5) {
                return [
                    'risk_score'         => 0.9,
                    'risk_numeric'       => 90,
                    'risk_level'         => 'high',
                    'is_anomaly'         => true,
                    'requires_3fa'       => true,
                    'explanation'        => ["{$failures} failed login attempts in last hour — 3FA required even on trusted device"],
                    'recommendation'     => '⚠️ Too many failures. 3FA required.',
                    'ip_address'         => $request->ip(),
                    'device_fingerprint' => $this->deviceFingerprint($request),
                ];
            }

            return [
                'risk_score'         => 0.05,
                'risk_numeric'       => 10,
                'risk_level'         => 'low',
                'is_anomaly'         => false,
                'requires_3fa'       => false,
                'explanation'        => ['Recognised device and IP — trusted session'],
                'recommendation'     => '✅ Trusted device. No additional verification needed.',
                'ip_address'         => $request->ip(),
                'device_fingerprint' => $this->deviceFingerprint($request),
            ];
        }

        $payload = $this->buildPayload($request, $user);

        try {
            $response = Http::timeout(4)
                ->post("{$this->apiUrl}/decide", $payload);

            if ($response->successful()) {
                $data = $this->normalizeDecisionResponse($response->json());
                // Attach device fingerprint for storage
                $data['device_fingerprint'] = $this->deviceFingerprint($request);
                $data['ip_address']         = $request->ip();

                // Hard rule: ≥5 failed attempts always triggers HIGH regardless of AI score
                $failures = $this->recentFailures($user);
                if ($failures >= 5 && !in_array($data['risk_level'] ?? '', ['high', 'critical'])) {
                    $data['risk_level']   = 'high';
                    $data['risk_numeric'] = max($data['risk_numeric'] ?? 70, 75);
                    $data['requires_3fa'] = true;
                    $data['explanation']  = array_merge(
                        $data['explanation'] ?? [],
                        ["{$failures} failed login attempts in the last hour — 3FA required"]
                    );
                }

                // Hard rule: cart ≥ 2,000,000 VND always triggers 3FA (high-value transaction)
                $cartValue = collect(session('cart', []))
                    ->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? $item['quantity'] ?? 1));
                if ($cartValue >= 2_000_000) {
                    $data['requires_3fa'] = true;
                    $data['is_anomaly']   = true;
                    if (!in_array($data['risk_level'] ?? '', ['high', 'critical'])) {
                        $data['risk_level']   = 'high';
                        $data['risk_numeric'] = max($data['risk_numeric'] ?? 60, 70);
                    }
                    $data['explanation'] = array_merge(
                        $data['explanation'] ?? [],
                        ['High-value transaction (≥ 2,000,000 ₫) — additional verification required']
                    );
                }

                return $data;
            }

            Log::warning('AI risk service returned HTTP ' . $response->status());
        } catch (\Throwable $e) {
            Log::warning('AI risk service unavailable: ' . $e->getMessage());
        }

        // Graceful fallback — check failed attempts manually
        return $this->fallback($request, $user);
    }

    /**
     * Build the real payload, merge in demo signal overrides, then call the AI engine.
     * This ensures demo log entries are 100% authentic AI-generated results.
     */
    public function assessWithOverrides(array $overrides, Request $request, User $user): array
    {
        $payload = array_merge($this->buildPayload($request, $user), $overrides);

        try {
            $response = Http::timeout(4)
                ->post("{$this->apiUrl}/decide", $payload);

            if ($response->successful()) {
                $data = $this->normalizeDecisionResponse($response->json());
                $data['device_fingerprint'] = $this->deviceFingerprint($request);
                $data['ip_address']         = $request->ip();
                $data['requires_3fa']       = true;
                return $data;
            }

            Log::warning('AI risk service returned HTTP ' . $response->status() . ' (demo override)');
        } catch (\Throwable $e) {
            Log::warning('AI risk service unavailable (demo override): ' . $e->getMessage());
        }

        return $this->fallback($request, $user);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function buildPayload(Request $request, User $user): array
    {
        $cartValue = collect(session('cart', []))
            ->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? $item['quantity'] ?? 1));

        $mouseMoveCountRaw = session('auth.mouse_move_count');
        $mouseAvgSpeedRaw  = session('auth.mouse_avg_speed');
        $hasMouseData      = $mouseMoveCountRaw !== null || $mouseAvgSpeedRaw !== null;
        $mouseMoveCount    = (int) ($mouseMoveCountRaw ?? 0);
        $mouseAvgSpeed     = (float) ($mouseAvgSpeedRaw ?? 0.0);
        $mouseRisk         = $hasMouseData
            ? $this->mouseBehaviorRisk($mouseMoveCount, $mouseAvgSpeed)
            : 0.0;

        $isNewIp = $this->isNewIp($request->ip(), $user);
        // If the IP is already trusted, don't flag a device mismatch as suspicious
        // (fingerprints can drift from browser updates/cleared cookies; OTP already verified identity)
        $isNewDevice = $isNewIp === 0 ? 0 : $this->isNewDevice($request, $user);

        $baseUaRisk = $this->uaRiskScore($request);
        $uaRisk     = min(1.0, round($baseUaRisk + $mouseRisk, 4));

        return [
            'user_id'               => $user->id,
            'ip'                    => $request->ip(),
            'device_id'             => $this->deviceFingerprint($request),
            'hour_of_day'           => now()->hour,
            'is_weekend'            => now()->isWeekend() ? 1 : 0,
            'is_new_ip'             => $isNewIp,
            'is_new_device'         => $isNewDevice,
            'failed_attempts'       => $this->recentFailures($user),
            'keystroke_speed_ms'    => (float) session('auth.keystroke_speed_ms', 150),
            'keystroke_irregularity'=> (float) session('auth.keystroke_irregularity', 30),
            'click_count_per_min'   => (int)   session('auth.click_count_per_min', 30),
            'mouse_move_count'      => $mouseMoveCount,
            'mouse_avg_speed'       => $mouseAvgSpeed,
            'transaction_amount'    => round($cartValue / 1000, 2), // → k VND
            'session_velocity'      => $this->sessionVelocity($user),
            'ua_risk_score'         => $uaRisk,
            'has_security_question' => ! empty($user->security_question) && ! empty($user->security_answer),
            'has_biometric'         => is_array($user->face_descriptor) && count($user->face_descriptor) >= 64,
            'account_age_days'      => (int) $user->created_at?->diffInDays(now()),
        ];
    }

    private function normalizeDecisionResponse(array $data): array
    {
        $riskNumeric = (int) ($data['risk_numeric'] ?? 20);
        $riskLevel   = strtolower((string) ($data['risk_level'] ?? 'low'));
        $score       = isset($data['risk_score'])
            ? (float) $data['risk_score']
            : $this->inferRiskScoreFromNumeric($riskNumeric);

        $explanation = $data['explanation'] ?? [];
        if (! is_array($explanation)) {
            $explanation = [$explanation];
        }

        $recommendations = $data['recommendations'] ?? [];
        if (! is_array($recommendations)) {
            $recommendations = [$recommendations];
        }

        return [
            'risk_score'      => round($score, 4),
            'risk_numeric'    => $riskNumeric,
            'risk_level'      => $riskLevel,
            'is_anomaly'      => in_array($riskLevel, ['high', 'critical'], true)
                || (bool) ($data['is_anomaly'] ?? false),
            'requires_3fa'    => (bool) ($data['requires_3fa'] ?? false),
            'explanation'     => array_values($explanation),
            'recommendation'  => (string) ($data['reason']
                ?? ($recommendations[0] ?? 'AI decision processed')),
            'challenge_type'  => $data['challenge_type'] ?? null,
            'decision_method' => $data['method'] ?? null,
            'action'          => $data['action'] ?? null,
        ];
    }

    private function inferRiskScoreFromNumeric(int $riskNumeric): float
    {
        $score = 0.5 - ($riskNumeric / 100);

        return max(-0.5, min(0.5, $score));
    }

    private function mouseBehaviorRisk(int $moveCount, float $avgSpeed): float
    {
        $risk = 0.0;

        if ($moveCount === 0) {
            return 0.4;
        }

        if ($moveCount < 8) {
            $risk += 0.2;
        }

        if ($avgSpeed > 1600) {
            $risk += 0.35;
        }

        if ($moveCount >= 120 && $avgSpeed < 8) {
            $risk += 0.25;
        }

        return min(1.0, $risk);
    }

    private function isNewIp(string $ip, User $user): int
    {
        $known = $user->known_ips ?? [];

        return in_array($ip, $known) ? 0 : 1;
    }

    private function isNewDevice(Request $request, User $user): int
    {
        $fp    = $this->deviceFingerprint($request);
        $known = $user->known_devices ?? [];

        return in_array($fp, $known) ? 0 : 1;
    }

    public function deviceFingerprint(Request $request): string
    {
        return md5($request->userAgent() ?? '');
    }

    private function recentFailures(User $user): int
    {
        $query = \App\Models\LoginAttempt::where('user_id', $user->id)
            ->where('password_ok', false);

        // Only count failures that happened AFTER the last successful login.
        // This resets the counter once the user successfully authenticates,
        // so old failures don't penalise future legitimate logins.
        if ($user->last_login_at) {
            $query->where('created_at', '>=', $user->last_login_at);
        } else {
            $query->where('created_at', '>=', now()->subHour());
        }

        return $query->count();
    }

    /**
     * Count successful logins (password + OTP both passed) in the last 24h.
     * Failed password attempts are already captured in recentFailures(), so exclude
     * them here to avoid counting one real login session multiple times.
     * High velocity indicates suspicious repeated re-authentication.
     */
    private function sessionVelocity(User $user): int
    {
        return \App\Models\LoginAttempt::where('user_id', $user->id)
            ->where('password_ok', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
    }

    /**
     * Score the User-Agent string for bot/automation risk.
     * Returns 0.0 for normal browsers, up to 1.0 for clear bots.
     */
    private function uaRiskScore(Request $request): float
    {
        $ua = strtolower($request->userAgent() ?? '');

        if (empty($ua)) {
            return 1.0; // No UA = almost certainly automated
        }

        // Known automation / scraping tools
        $suspicion = ['curl/', 'python-requests', 'python/', 'scrapy', 'wget/',
                      'http-client', 'java/', 'libwww', 'go-http', 'axios',
                      'node-fetch', 'okhttp', 'mechanize', 'perl/'
        ];
        foreach ($suspicion as $sig) {
            if (str_contains($ua, $sig)) {
                return 0.9;
            }
        }

        // Very short UAs are suspicious
        if (strlen($ua) < 20) {
            return 0.7;
        }

        // Headless / testing browsers
        if (str_contains($ua, 'headlesschrome') || str_contains($ua, 'phantomjs')
            || str_contains($ua, 'selenium')) {
            return 0.95;
        }

        return 0.0; // Normal browser
    }

    /**
     * Remember this IP + device as known after a successful login.
     */
    public function rememberDevice(Request $request, User $user): void
    {
        $ip = $request->ip();
        $fp = $this->deviceFingerprint($request);

        $knownIps = collect($user->known_ips ?? [])
            ->push($ip)->unique()->take(20)->values()->all();

        $knownDevices = collect($user->known_devices ?? [])
            ->push($fp)->unique()->take(10)->values()->all();

        $user->update([
            'known_ips'     => $knownIps,
            'known_devices' => $knownDevices,
        ]);
    }

    private function fallback(Request $request, User $user): array
    {
        // Only count failures within the last 30 minutes to avoid penalising
        // users for old failed attempts made hours ago.
        $failures = \App\Models\LoginAttempt::where('user_id', $user->id)
            ->where('password_ok', false)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->count();

        if ($failures >= 5) {
            return [
                'risk_score'     => 0.9,
                'risk_numeric'   => 90,
                'risk_level'     => 'high',
                'is_anomaly'     => true,
                'requires_3fa'   => true,
                'explanation'    => ['AI service unavailable — elevated risk: ' . $failures . ' failed login attempts in last 30 minutes'],
                'recommendation' => '⚠️ AI offline but high risk detected. 3FA required.',
                'ip_address'     => $request->ip(),
                'device_fingerprint' => $this->deviceFingerprint($request),
            ];
        }

        return [
            'risk_score'     => 0.1,
            'risk_numeric'   => 20,
            'risk_level'     => 'low',
            'is_anomaly'     => false,
            'requires_3fa'   => false,
            'explanation'    => ['AI service unavailable — fallback to LOW risk'],
            'recommendation' => '✅ AI offline. Login allowed (fallback mode).',
            'ip_address'     => $request->ip(),
            'device_fingerprint' => $this->deviceFingerprint($request),
        ];
    }

    /**
     * GeoIP lookup via the Python AI service's /geoip/{ip} endpoint.
     * Returns array with country_code, city, is_vn, is_known_vpn_country, etc.
     * Falls back to empty array if the AI service is down.
     */
    public function geoIp(string $ip): array
    {
        // Skip private/loopback IPs (development)
        if ($ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return ['country' => 'Local', 'country_code' => 'VN', 'city' => 'Localhost', 'is_vn' => true, 'is_known_vpn_country' => false];
        }
        try {
            $resp = Http::timeout(3)
                ->get("{$this->apiUrl}/geoip/{$ip}");
            if ($resp->successful()) {
                return $resp->json();
            }
        } catch (\Throwable) {}
        return [];
    }
}
