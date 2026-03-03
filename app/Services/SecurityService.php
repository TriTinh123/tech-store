<?php

namespace App\Services;

use App\Models\AdminSecuritySetting;
use App\Models\LoginAttempt;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SecurityService
{
    private $pythonApiUrl;

    private $apiSecret;

    private $maxLoginAttempts;

    private $lockoutDurationMinutes;

    public function __construct()
    {
        $this->pythonApiUrl = config('services.python_security_api.url', 'http://localhost:5000');
        $this->apiSecret = config('services.python_security_api.secret');

        $settings = AdminSecuritySetting::getInstance();
        $this->maxLoginAttempts = $settings->max_login_attempts ?? 5;
        $this->lockoutDurationMinutes = $settings->lockout_duration_minutes ?? 15;
    }

    /**
     * Check if login attempt has anomalies
     */
    public function checkLoginAnomalies($email, $ipAddress, $userAgent, $userId = null)
    {
        try {
            // Get device fingerprint
            $deviceFingerprint = $this->generateDeviceFingerprint($userAgent);

            // Get location from IP
            $location = $this->getLocationFromIp($ipAddress);

            // Get recent login attempts for this user
            $recentFailedAttempts = LoginAttempt::where('email', $email)
                ->where('status', 'failed')
                ->where('created_at', '>=', now()->subMinutes(30))
                ->count();

            // Check for new device
            $isNewDevice = false;
            $isNewLocation = false;

            if ($userId) {
                $isNewDevice = ! LoginAttempt::where('user_id', $userId)
                    ->where('device_fingerprint', $deviceFingerprint)
                    ->where('status', 'success')
                    ->exists();

                $isNewLocation = ! LoginAttempt::where('user_id', $userId)
                    ->where('location', $location['location'])
                    ->where('status', 'success')
                    ->exists();
            }

            // Prepare payload for Python API
            $payload = [
                'email' => $email,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_fingerprint' => $deviceFingerprint,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'location' => $location['location'],
                'failed_attempts' => $recentFailedAttempts,
                'is_new_device' => $isNewDevice,
                'is_new_location' => $isNewLocation,
                'login_hour' => now()->hour,
                'user_id' => $userId,
            ];

            // Call Python API
            $response = Http::timeout(10)->post(
                "{$this->pythonApiUrl}/api/check-login-anomaly",
                $payload
            );

            if ($response->successful()) {
                $anomalyData = $response->json();

                // Create login attempt record
                $loginAttempt = LoginAttempt::create([
                    'user_id' => $userId,
                    'email' => $email,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'device_fingerprint' => $deviceFingerprint,
                    'location' => $location['location'],
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'status' => 'pending', // Will be updated after auth
                    'is_suspicious' => $anomalyData['is_suspicious'] ?? false,
                    'anomaly_type' => implode(',', $anomalyData['anomaly_types'] ?? []),
                ]);

                return [
                    'success' => true,
                    'is_suspicious' => $anomalyData['is_suspicious'] ?? false,
                    'risk_score' => $anomalyData['risk_score'] ?? 0,
                    'anomaly_types' => $anomalyData['anomaly_types'] ?? [],
                    'requires_3fa' => $anomalyData['requires_3fa'] ?? false,
                    'should_lock_account' => $anomalyData['should_lock_account'] ?? false,
                    'login_attempt_id' => $loginAttempt->id,
                ];
            }

            Log::error('Python API error: '.$response->status());

            return [
                'success' => false,
                'error' => 'Security check failed',
            ];

        } catch (\Exception $e) {
            Log::error('Security service error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate device fingerprint
     */
    public function generateDeviceFingerprint($userAgent)
    {
        try {
            $response = Http::timeout(5)->post(
                "{$this->pythonApiUrl}/api/generate-device-fingerprint",
                [
                    'user_agent' => $userAgent,
                    'accept_language' => request()->header('Accept-Language', 'en-US'),
                    'timezone' => config('app.timezone', 'UTC'),
                ]
            );

            if ($response->successful()) {
                return $response->json('device_fingerprint');
            }
        } catch (\Exception $e) {
            Log::error('Device fingerprint error: '.$e->getMessage());
        }

        // Fallback: hash user agent
        return hash('sha256', $userAgent);
    }

    /**
     * Get location from IP address
     */
    public function getLocationFromIp($ipAddress)
    {
        try {
            $response = Http::timeout(5)->post(
                "{$this->pythonApiUrl}/api/get-location-from-ip",
                ['ip_address' => $ipAddress]
            );

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Geolocation error: '.$e->getMessage());
        }

        // Fallback
        return [
            'ip_address' => $ipAddress,
            'location' => 'Unknown',
            'latitude' => 0,
            'longitude' => 0,
            'country' => 'Unknown',
            'city' => 'Unknown',
        ];
    }

    /**
     * Record login attempt as success or failed
     */
    public function recordLoginAttempt($loginAttemptId, $status, $userId = null)
    {
        try {
            $loginAttempt = LoginAttempt::find($loginAttemptId);
            if ($loginAttempt) {
                $loginAttempt->update([
                    'status' => $status,
                    'user_id' => $userId,
                ]);

                // Create security event
                if ($status === 'failed') {
                    $failedCount = LoginAttempt::where('email', $loginAttempt->email)
                        ->where('status', 'failed')
                        ->where('created_at', '>=', now()->subMinutes(30))
                        ->count();

                    SecurityEvent::create([
                        'user_id' => $userId,
                        'email' => $loginAttempt->email,
                        'ip_address' => $loginAttempt->ip_address,
                        'event_type' => 'login_attempt_failed',
                        'severity' => $failedCount >= $this->maxLoginAttempts ? 'high' : 'low',
                        'description' => "Failed login attempt from {$loginAttempt->location}",
                        'context' => [
                            'device_fingerprint' => $loginAttempt->device_fingerprint,
                            'location' => $loginAttempt->location,
                            'is_suspicious' => $loginAttempt->is_suspicious,
                            'anomaly_type' => $loginAttempt->anomaly_type,
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Record login attempt error: '.$e->getMessage());
        }
    }

    /**
     * Check if account is locked due to too many failed attempts
     */
    public function isAccountLocked($email)
    {
        $settings = AdminSecuritySetting::getInstance();
        $maxAttempts = $settings->max_login_attempts ?? 5;
        $lockoutMinutes = $settings->lockout_duration_minutes ?? 15;

        $failedAttempts = LoginAttempt::where('email', $email)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes($lockoutMinutes))
            ->count();

        return $failedAttempts >= $maxAttempts;
    }

    /**
     * Unlock account (admin function)
     */
    public function unlockAccount($email)
    {
        LoginAttempt::where('email', $email)
            ->where('status', 'failed')
            ->delete();

        SecurityEvent::create([
            'email' => $email,
            'event_type' => 'account_unlocked_by_admin',
            'severity' => 'medium',
            'description' => 'Account unlocked by administrator',
            'is_resolved' => true,
        ]);
    }

    /**
     * Create security alert
     */
    public function createSecurityAlert($userId, $eventType, $severity = 'medium', $description = '', $context = [], $ipAddress = 'unknown')
    {
        $user = \App\Models\User::find($userId);

        SecurityEvent::create([
            'user_id' => $userId,
            'email' => $user->email ?? '',
            'ip_address' => $ipAddress,
            'event_type' => $eventType,
            'severity' => $severity,
            'description' => $description,
            'context' => $context,
            'is_resolved' => false,
        ]);
    }

    /**
     * Get user's active session count
     */
    public function getActiveSessionCount($userId)
    {
        return \App\Models\UserSession::where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();
    }

    /**
     * Check if 3FA is enabled for user
     */
    public function is3faRequired($userId, $riskScore = 0)
    {
        $user = \App\Models\User::find($userId);
        if (! $user) {
            return false;
        }

        $settings = AdminSecuritySetting::getInstance();

        // 3FA always required if enabled globally
        if ($settings->enable_3fa) {
            return $user->is3faEnabled();
        }

        // Require 3FA if risk score is high
        if ($riskScore > 0.6) {
            return true;
        }

        return false;
    }
}
