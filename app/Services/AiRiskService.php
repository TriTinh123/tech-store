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
        $payload = $this->buildPayload($request, $user);

        try {
            $response = Http::timeout(4)
                ->post("{$this->apiUrl}/score", $payload);

            if ($response->successful()) {
                $data = $response->json();
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

                return $data;
            }

            Log::warning('AI risk service returned HTTP ' . $response->status());
        } catch (\Throwable $e) {
            Log::warning('AI risk service unavailable: ' . $e->getMessage());
        }

        // Graceful fallback — check failed attempts manually
        return $this->fallback($request, $user);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function buildPayload(Request $request, User $user): array
    {
        $cartValue = collect(session('cart', []))
            ->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? $item['quantity'] ?? 1));

        $isNewIp = $this->isNewIp($request->ip(), $user);
        // If the IP is already trusted, don't flag a device mismatch as suspicious
        // (fingerprints can drift from browser updates/cleared cookies; OTP already verified identity)
        $isNewDevice = $isNewIp === 0 ? 0 : $this->isNewDevice($request, $user);

        return [
            'user_id'               => $user->id,
            'hour_of_day'           => now()->hour,
            'is_weekend'            => now()->isWeekend() ? 1 : 0,
            'is_new_ip'             => $isNewIp,
            'is_new_device'         => $isNewDevice,
            'failed_attempts'       => $this->recentFailures($user),
            'keystroke_speed_ms'    => (float) session('auth.keystroke_speed_ms', 150),
            'keystroke_irregularity'=> (float) session('auth.keystroke_irregularity', 30),
            'click_count_per_min'   => (int)   session('auth.click_count_per_min', 30),
            'transaction_amount'    => round($cartValue / 1000, 2), // → k VND
            'session_velocity'      => $this->sessionVelocity($user),
            'ua_risk_score'         => $this->uaRiskScore($request),
        ];
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
        return \App\Models\LoginAttempt::where('user_id', $user->id)
            ->where('password_ok', false)
            ->where('created_at', '>=', now()->subHour())
            ->count();
    }

    /**
     * Count all login attempts (successful + failed) for this user in the last 24h.
     * High velocity indicates automated/brute-force activity.
     */
    private function sessionVelocity(User $user): int
    {
        return \App\Models\LoginAttempt::where('user_id', $user->id)
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
        $failures = $this->recentFailures($user);

        if ($failures >= 3) {
            return [
                'risk_score'     => 0.9,
                'risk_numeric'   => 90,
                'risk_level'     => 'high',
                'is_anomaly'     => true,
                'requires_3fa'   => true,
                'explanation'    => ['AI service unavailable — elevated risk: ' . $failures . ' failed login attempts detected'],
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
