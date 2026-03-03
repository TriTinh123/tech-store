<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\SuspiciousLogin;
use App\Models\User;

class AnomalyDetectionService
{
    protected AlertService $alertService;

    protected AutoResponseService $autoResponseService;

    public function __construct(AlertService $alertService, AutoResponseService $autoResponseService)
    {
        $this->alertService = $alertService;
        $this->autoResponseService = $autoResponseService;
    }

    // Configuration
    const MAX_FAILED_ATTEMPTS = 5;

    const LOCKOUT_DURATION_MINUTES = 15;

    const UNUSUAL_HOUR_RANGE = [0, 1, 2, 3, 4, 5]; // Midnight to 5 AM

    const LOCATION_CHANGE_THRESHOLD_KM = 900; // 900km in 1 hour = suspicious

    /**
     * Check for anomalies before login
     */
    public function checkLoginAnomaly(User $user, string $ipAddress, string $userAgent): array
    {
        $anomalies = [];
        $riskLevel = 'low';

        // Check 1: Account Lockout
        if ($this->isAccountLocked($user)) {
            return [
                'is_anomaly' => true,
                'risk_level' => 'critical',
                'reason' => 'account_locked',
                'message' => 'Account is locked due to multiple failed login attempts. Try again after '.self::LOCKOUT_DURATION_MINUTES.' minutes.',
            ];
        }

        // Check 2: New IP Address
        if ($this->isNewIpAddress($user, $ipAddress)) {
            $anomalies[] = 'new_ip_address';
            $riskLevel = 'medium';
        }

        // Check 3: New Device
        $deviceFingerprint = $this->generateDeviceFingerprint($userAgent, $ipAddress);
        if ($this->isNewDevice($user, $deviceFingerprint)) {
            $anomalies[] = 'new_device';
            $riskLevel = 'medium';
        }

        // Check 4: Unusual Login Time
        if ($this->isUnusualTime($user)) {
            $anomalies[] = 'unusual_time';
            if ($riskLevel === 'low') {
                $riskLevel = 'low'; // Keep as low unless combined with other factors
            }
        }

        // Check 5: Rapid Location Change
        if ($this->isRapidLocationChange($user, $ipAddress)) {
            $anomalies[] = 'rapid_location_change';
            $riskLevel = 'high';
        }

        // Check 6: Multiple Failed Attempts
        $failedAttempts = $this->getRecentFailedAttempts($user);
        if ($failedAttempts >= self::MAX_FAILED_ATTEMPTS - 2) {
            $anomalies[] = 'multiple_failed_attempts';
            $riskLevel = 'high';
        }

        return [
            'is_anomaly' => count($anomalies) > 0,
            'risk_level' => $riskLevel,
            'anomalies' => $anomalies,
            'device_fingerprint' => $deviceFingerprint,
        ];
    }

    /**
     * Check if account is locked due to failed attempts
     */
    public function isAccountLocked(User $user): bool
    {
        $failedAttempts = LoginAttempt::where('user_id', $user->id)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subMinutes(self::LOCKOUT_DURATION_MINUTES))
            ->count();

        return $failedAttempts >= self::MAX_FAILED_ATTEMPTS;
    }

    /**
     * Check if IP address is new
     */
    public function isNewIpAddress(User $user, string $ipAddress): bool
    {
        $existingIp = LoginAttempt::where('user_id', $user->id)
            ->where('ip_address', $ipAddress)
            ->where('success', true)
            ->exists();

        return ! $existingIp;
    }

    /**
     * Check if device is new (based on user agent + IP fingerprint)
     */
    public function isNewDevice(User $user, string $deviceFingerprint): bool
    {
        $existingDevice = LoginAttempt::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->where('success', true)
            ->exists();

        return ! $existingDevice;
    }

    /**
     * Check if login time is unusual
     */
    public function isUnusualTime(User $user): bool
    {
        $currentHour = now()->hour;

        // Check if current hour is in unusual range (midnight to 5 AM)
        if (in_array($currentHour, self::UNUSUAL_HOUR_RANGE)) {
            // Check if user typically logs in at this hour
            $typicalLogins = LoginAttempt::where('user_id', $user->id)
                ->where('success', true)
                ->whereRaw('HOUR(attempted_at) = ?', [$currentHour])
                ->count();

            // If user has never logged in at this hour and it's midnight-5AM, it's unusual
            return $typicalLogins === 0;
        }

        return false;
    }

    /**
     * Check for rapid location change (travel impossible)
     */
    public function isRapidLocationChange(User $user, string $newIp): bool
    {
        $lastLogin = LoginAttempt::where('user_id', $user->id)
            ->where('success', true)
            ->latest('attempted_at')
            ->first();

        if (! $lastLogin) {
            return false;
        }

        // If last login was less than 1 hour ago
        $timeSinceLastLogin = $lastLogin->attempted_at->diffInMinutes(now());
        if ($timeSinceLastLogin < 60) {
            // Try to get coordinates (simplified - in production use geocoding API)
            $lastIpLocation = $this->getIpLocation($lastLogin->ip_address);
            $newIpLocation = $this->getIpLocation($newIp);

            if ($lastIpLocation && $newIpLocation) {
                // Calculate distance (simplified haversine)
                $distance = $this->calculateDistance(
                    $lastIpLocation['lat'], $lastIpLocation['lon'],
                    $newIpLocation['lat'], $newIpLocation['lon']
                );

                // If more than LOCATION_CHANGE_THRESHOLD_KM km away
                return $distance > self::LOCATION_CHANGE_THRESHOLD_KM;
            }
        }

        return false;
    }

    /**
     * Get recent failed login attempts
     */
    public function getRecentFailedAttempts(User $user): int
    {
        return LoginAttempt::where('user_id', $user->id)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subHours(1))
            ->count();
    }

    /**
     * Record suspicious login
     */
    public function recordSuspiciousLogin(User $user, string $ipAddress, string $userAgent, string $reason, string $riskLevel): SuspiciousLogin
    {
        $location = $this->getIpLocation($ipAddress) ?? [];

        $suspiciousLogin = SuspiciousLogin::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'country' => $location['country'] ?? 'Unknown',
            'city' => $location['city'] ?? 'Unknown',
            'device_type' => $this->getDeviceType($userAgent),
            'browser' => $this->getBrowserInfo($userAgent),
            'risk_level' => $riskLevel,
            'reason' => $reason,
        ]);

        // Dispatch security alerts through AlertService
        $this->dispatchAnomalyAlerts($user, $suspiciousLogin, $reason, $riskLevel, $ipAddress, $userAgent, $location);

        // Handle automatic response based on severity
        try {
            $severity = $this->mapRiskLevelToSeverity($riskLevel);
            $this->autoResponseService->handleAnomalyResponse($user, $suspiciousLogin, $severity);
        } catch (\Exception $e) {
            // Log the error but don't interrupt the login flow
            \Log::error('AutoResponse handling failed: '.$e->getMessage(), [
                'user_id' => $user->id,
                'suspicious_login_id' => $suspiciousLogin->id,
            ]);
        }

        return $suspiciousLogin;
    }

    /**
     * Dispatch alerts for detected anomalies
     */
    private function dispatchAnomalyAlerts(
        User $user,
        SuspiciousLogin $suspiciousLogin,
        string $reason,
        string $riskLevel,
        string $ipAddress,
        string $userAgent,
        array $location
    ): void {
        // Determine alert type from reason
        $reasonArray = explode(',', trim($reason));
        $mainReason = trim($reasonArray[0]);

        $alertType = $this->mapReasonToAlertType($mainReason);
        $severity = $this->mapRiskLevelToSeverity($riskLevel);
        $message = $this->buildAlertMessage($mainReason, $ipAddress, $userAgent, $location);

        // Send alert to user
        $this->alertService->dispatchAlert(
            user: $user,
            alertType: $alertType,
            message: $message,
            severity: $severity,
            suspiciousLogin: $suspiciousLogin,
            channels: $this->getNotificationChannels($user)
        );
    }

    /**
     * Map reason string to alert type enum
     */
    private function mapReasonToAlertType(string $reason): string
    {
        return match (true) {
            str_contains($reason, 'new_ip') => 'new_ip',
            str_contains($reason, 'new_device') => 'new_device',
            str_contains($reason, 'unusual_time') => 'unusual_time',
            str_contains($reason, 'rapid_location') => 'rapid_location',
            str_contains($reason, 'multiple_failed') => 'failed_attempt',
            str_contains($reason, 'account_locked') => 'account_locked',
            default => 'failed_attempt',
        };
    }

    /**
     * Map risk level to severity
     */
    public function mapRiskLevelToSeverity(string $riskLevel): string
    {
        return match ($riskLevel) {
            'critical' => 'critical',
            'high' => 'high',
            'medium' => 'medium',
            'low' => 'low',
            default => 'medium',
        };
    }

    /**
     * Build alert message for anomaly
     */
    private function buildAlertMessage(
        string $reason,
        string $ipAddress,
        string $userAgent,
        array $location
    ): string {
        $deviceType = $this->getDeviceType($userAgent);
        $browser = $this->getBrowserInfo($userAgent);
        $city = $location['city'] ?? 'Unknown location';
        $time = now()->format('H:i:s d/m/Y');

        return match (true) {
            str_contains($reason, 'new_ip') => "⚠️ Phát hiện đăng nhập từ IP mới ({$ipAddress}) tại {$city} vào lúc {$time}.",
            str_contains($reason, 'new_device') => "⚠️ Phát hiện đăng nhập từ thiết bị lạ ({$deviceType} - {$browser}) vào lúc {$time}.",
            str_contains($reason, 'unusual_time') => "⚠️ Phát hiện đăng nhập vào thời gian bất thường ({$time}) từ IP {$ipAddress}.",
            str_contains($reason, 'rapid_location') => "⚠️ Phát hiện đăng nhập từ vị trí quá xa (vị trí hiện tại: {$city}) vào lúc {$time}.",
            str_contains($reason, 'multiple_failed') => "⚠️ Phát hiện nhiều lần đăng nhập thất bại vào lúc {$time}. Kiểm tra ngay nếu đây không phải bạn.",
            str_contains($reason, 'account_locked') => '🔒 Tài khoản của bạn đã bị khóa tạm thời do quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau 15 phút.',
            default => "⚠️ Phát hiện hoạt động bất thường trên tài khoản của bạn vào lúc {$time}.",
        };
    }

    /**
     * Get notification channels for user (based on preferences)
     * For now, always use email as default
     */
    private function getNotificationChannels(User $user): array
    {
        // TODO: Fetch from user notification preferences
        // For now, always send email
        return ['email'];
    }

    /**
     * Generate device fingerprint from user agent and IP
     */
    public function generateDeviceFingerprint(string $userAgent, string $ipAddress): string
    {
        return hash('sha256', $userAgent.'|'.$ipAddress);
    }

    /**
     * Get device type from user agent
     */
    public function getDeviceType(string $userAgent): string
    {
        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/ipad|android|tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Get browser info from user agent
     */
    public function getBrowserInfo(string $userAgent): string
    {
        if (preg_match('/chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/edge|edg\//i', $userAgent)) {
            return 'Edge';
        }

        return 'Unknown';
    }

    /**
     * Get IP location (simplified - in production use MaxMind GeoIP2 or similar)
     */
    public function getIpLocation(string $ipAddress): ?array
    {
        // This is a simplified version - in production use a real GeoIP API
        // For now, return localhost/private IPs as Vietnam for testing
        if (in_array($ipAddress, ['127.0.0.1', 'localhost', '::1'])) {
            return [
                'country' => 'Vietnam',
                'city' => 'Ho Chi Minh City',
                'lat' => 10.8231,
                'lon' => 106.6538,
            ];
        }

        // TODO: Integrate with MaxMind GeoIP2, IPQualityScore, or similar API
        return null;
    }

    /**
     * Calculate distance between two coordinates (haversine formula)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Clear account lockout
     */
    public function clearAccountLockout(User $user): void
    {
        LoginAttempt::where('user_id', $user->id)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subMinutes(self::LOCKOUT_DURATION_MINUTES))
            ->delete();
    }

    /**
     * Get anomaly score for user (0-100)
     */
    public function getAnomalyScore(User $user, array $anomalyData): int
    {
        $score = 0;
        $anomalies = $anomalyData['anomalies'] ?? [];

        // Each anomaly adds points
        foreach ($anomalies as $anomaly) {
            switch ($anomaly) {
                case 'new_ip_address':
                    $score += 15;
                    break;
                case 'new_device':
                    $score += 15;
                    break;
                case 'unusual_time':
                    $score += 10;
                    break;
                case 'rapid_location_change':
                    $score += 40;
                    break;
                case 'multiple_failed_attempts':
                    $score += 25;
                    break;
            }
        }

        return min($score, 100);
    }

    /**
     * Get user's trusted devices
     */
    public function getTrustedDevices(User $user)
    {
        return LoginAttempt::where('user_id', $user->id)
            ->where('success', true)
            ->select('device_fingerprint', 'ip_address')
            ->distinct()
            ->get();
    }

    /**
     * Add trusted device
     */
    public function addTrustedDevice(User $user, string $deviceFingerprint, string $ipAddress): void
    {
        // Device is trusted after first successful login
        // This is inherent in the LoginAttempt record
    }
}
