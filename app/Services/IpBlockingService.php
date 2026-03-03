<?php

namespace App\Services;

use App\Models\BlockedIp;
use App\Models\LoginAttempt;
use App\Models\SuspiciousLogin;
use Illuminate\Support\Facades\Http;

class IpBlockingService
{
    /**
     * Check if an IP address is currently blocked
     */
    public function isIpBlocked($ipAddress)
    {
        $blockedIp = BlockedIp::where('ip_address', $ipAddress)->first();

        if (! $blockedIp) {
            return false;
        }

        // If permanently blocked, return true
        if ($blockedIp->is_permanent) {
            return true;
        }

        // If temporary block has expired, unblock it
        if ($blockedIp->unblock_at && $blockedIp->unblock_at->isPast()) {
            $blockedIp->unblock();

            return false;
        }

        // If temporary block is still active
        if ($blockedIp->unblock_at && $blockedIp->unblock_at->isFuture()) {
            return true;
        }

        return false;
    }

    /**
     * Get blocked IP details
     */
    public function getBlockedIpDetails($ipAddress)
    {
        return BlockedIp::where('ip_address', $ipAddress)->first();
    }

    /**
     * Manually block an IP address
     */
    public function blockIp($ipAddress, $reason = null, $adminId = null, $isPermanent = false, $blockDurationMinutes = 60)
    {
        // Check if already blocked
        $existingBlock = BlockedIp::where('ip_address', $ipAddress)->first();
        if ($existingBlock) {
            return $existingBlock;
        }

        // Get IP geolocation
        $geoData = $this->getIpGeolocation($ipAddress);

        $unblockAt = $isPermanent ? null : now()->addMinutes($blockDurationMinutes);

        $blockedIp = BlockedIp::create([
            'ip_address' => $ipAddress,
            'country_code' => $geoData['country_code'] ?? null,
            'location' => $geoData['location'] ?? null,
            'block_type' => 'manual',
            'reason' => $reason,
            'is_permanent' => $isPermanent,
            'blocked_at' => now(),
            'unblock_at' => $unblockAt,
            'blocked_by_admin_id' => $adminId,
            'risk_level' => 'high',
            'history' => [[
                'action' => 'manually_blocked',
                'timestamp' => now()->toIso8601String(),
                'reason' => $reason,
                'admin_id' => $adminId,
            ]],
        ]);

        return $blockedIp;
    }

    /**
     * Auto-block an IP after failed attempts
     */
    public function autoBlockIpAfterFailedAttempts($ipAddress, $userId = null, $failedAttempts = 0)
    {
        // Check if already blocked
        if ($this->isIpBlocked($ipAddress)) {
            return null;
        }

        // Get recent failed attempts from this IP
        $attempts = LoginAttempt::where('ip_address', $ipAddress)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        // Get security config
        $securityConfig = \App\Models\SecurityConfig::getCurrent();
        if (! $securityConfig || ! $securityConfig->enable_ip_blocking) {
            return null;
        }

        // If attempts exceed threshold
        if ($attempts >= $securityConfig->block_ips_after_failed_attempts) {
            $geoData = $this->getIpGeolocation($ipAddress);
            $blockDuration = $securityConfig->ip_block_duration_minutes ?? 60;

            $blockedIp = BlockedIp::create([
                'ip_address' => $ipAddress,
                'country_code' => $geoData['country_code'] ?? null,
                'location' => $geoData['location'] ?? null,
                'block_type' => 'auto_failed_attempts',
                'reason' => "Automatic block: {$attempts} failed attempts in 15 minutes",
                'is_permanent' => false,
                'blocked_at' => now(),
                'unblock_at' => now()->addMinutes($blockDuration),
                'failed_attempts' => $attempts,
                'total_login_attempts' => LoginAttempt::where('ip_address', $ipAddress)->count(),
                'last_attempt_at' => now(),
                'risk_level' => 'high',
                'history' => [[
                    'action' => 'auto_blocked_failed_attempts',
                    'timestamp' => now()->toIso8601String(),
                    'failed_attempts' => $attempts,
                    'block_duration_minutes' => $blockDuration,
                ]],
            ]);

            return $blockedIp;
        }

        return null;
    }

    /**
     * Auto-block IP on detected attack pattern
     */
    public function autoBlockIpOnAttack($ipAddress, $attackType = 'brute_force', $adminId = null)
    {
        // Check if already blocked
        if ($this->isIpBlocked($ipAddress)) {
            return null;
        }

        $geoData = $this->getIpGeolocation($ipAddress);

        $blockedIp = BlockedIp::create([
            'ip_address' => $ipAddress,
            'country_code' => $geoData['country_code'] ?? null,
            'location' => $geoData['location'] ?? null,
            'block_type' => 'auto_attack',
            'reason' => "Automatic block: {$attackType} attack detected",
            'is_permanent' => false,
            'blocked_at' => now(),
            'unblock_at' => now()->addHours(2),
            'risk_level' => 'critical',
            'suspicious_patterns' => [
                'attack_type' => $attackType,
                'detected_at' => now()->toIso8601String(),
            ],
            'history' => [[
                'action' => 'auto_blocked_attack',
                'timestamp' => now()->toIso8601String(),
                'attack_type' => $attackType,
            ]],
        ]);

        return $blockedIp;
    }

    /**
     * Unblock an IP address
     */
    public function unblockIp($ipAddress, $adminId = null)
    {
        $blockedIp = BlockedIp::where('ip_address', $ipAddress)->first();

        if (! $blockedIp) {
            return false;
        }

        $blockedIp->addToHistory('manually_unblocked', [
            'unblocked_at' => now(),
            'admin_id' => $adminId,
        ]);
        $blockedIp->unblock();

        return true;
    }

    /**
     * Get all currently active blocked IPs
     */
    public function getActiveBlockedIps()
    {
        return BlockedIp::active()->latest('blocked_at')->get();
    }

    /**
     * Get blocked IPs by risk level
     */
    public function getBlockedIpsByRiskLevel($riskLevel)
    {
        return BlockedIp::active()
            ->where('risk_level', $riskLevel)
            ->latest('blocked_at')
            ->get();
    }

    /**
     * Get blocked IPs by country
     */
    public function getBlockedIpsByCountry($countryCode)
    {
        return BlockedIp::active()
            ->where('country_code', $countryCode)
            ->latest('blocked_at')
            ->get();
    }

    /**
     * Get statistics about blocked IPs
     */
    public function getBlockingStatistics()
    {
        return [
            'total_blocked' => BlockedIp::count(),
            'currently_active' => BlockedIp::active()->count(),
            'permanent_blocks' => BlockedIp::permanent()->count(),
            'temporary_blocks' => BlockedIp::temporary()->count(),
            'expired_blocks' => BlockedIp::expired()->count(),
            'auto_blocked' => BlockedIp::autoBlocked()->count(),
            'manual_blocked' => BlockedIp::manual()->count(),
            'critical_risk' => BlockedIp::active()->critical()->count(),
            'by_block_type' => BlockedIp::active()
                ->groupBy('block_type')
                ->selectRaw('block_type, count(*) as count')
                ->pluck('count', 'block_type'),
            'by_risk_level' => BlockedIp::active()
                ->groupBy('risk_level')
                ->selectRaw('risk_level, count(*) as count')
                ->pluck('count', 'risk_level'),
        ];
    }

    /**
     * Clean up expired blocks (run via scheduler)
     */
    public function cleanupExpiredBlocks()
    {
        $expiredBlocks = BlockedIp::where('is_permanent', false)
            ->where('unblock_at', '<=', now())
            ->get();

        foreach ($expiredBlocks as $block) {
            if (method_exists($block, 'addToHistory')) {
                $block->addToHistory('auto_unblocked_expired', ['expired_at' => now()]);
            }
            if (method_exists($block, 'unblock')) {
                $block->unblock();
            }
        }

        return count($expiredBlocks);
    }

    /**
     * Get IP geolocation data
     */
    private function getIpGeolocation($ipAddress)
    {
        try {
            // Using ip-api.com free tier (no API key needed, limited to 45 requests/minute)
            $response = Http::get('http://ip-api.com/json/'.$ipAddress, [
                'fields' => 'country,countryCode,city,region',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'country_code' => $data['countryCode'] ?? null,
                    'location' => ($data['city'] ?? '').', '.($data['region'] ?? '').', '.($data['country'] ?? ''),
                ];
            }
        } catch (\Exception $e) {
            // Silently fall back to null if geolocation fails
        }

        return [
            'country_code' => null,
            'location' => null,
        ];
    }

    /**
     * Get countries with most blocked IPs
     */
    public function getTopBlockedCountries($limit = 10)
    {
        return BlockedIp::active()
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->selectRaw('country_code, count(*) as count')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'country_code');
    }

    /**
     * Get most frequently blocked locations
     */
    public function getMostBlockedLocations($limit = 10)
    {
        return BlockedIp::active()
            ->whereNotNull('location')
            ->groupBy('location')
            ->selectRaw('location, count(*) as count')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'location');
    }

    /**
     * Check if an IP should be auto-blocked based on anomalies
     */
    public function shouldAutoBlockIp($ipAddress, $suspiciousLoginsInLastHour = 0)
    {
        $securityConfig = \App\Models\SecurityConfig::getCurrent();

        if (! $securityConfig || ! $securityConfig->enable_ip_blocking) {
            return false;
        }

        // Get recent suspicious activities from this IP
        $suspiciousCount = SuspiciousLogin::where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        // If more than 3 suspicious activities in 1 hour, mark for blocking
        return $suspiciousCount >= 3;
    }

    /**
     * Update IP block information
     */
    public function updateBlockInfo($ipAddress, $data = [])
    {
        $blockedIp = BlockedIp::where('ip_address', $ipAddress)->first();

        if (! $blockedIp) {
            return false;
        }

        if (isset($data['reason'])) {
            $blockedIp->reason = $data['reason'];
        }

        if (isset($data['risk_level'])) {
            $blockedIp->risk_level = $data['risk_level'];
        }

        if (isset($data['extend_block_minutes'])) {
            if ($blockedIp->unblock_at) {
                $blockedIp->unblock_at = $blockedIp->unblock_at->addMinutes($data['extend_block_minutes']);
            }
        }

        if (isset($data['notes'])) {
            $blockedIp->notes = $data['notes'];
        }

        $blockedIp->addToHistory('updated', $data);
        $blockedIp->save();

        return true;
    }
}
