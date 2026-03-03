<?php

namespace App\Services;

use App\Models\BiometricData;
use App\Models\OtpSetting;
use App\Models\SecurityQuestion;
use App\Models\ThreeFactorAuthLog;
use App\Models\User;
use App\Models\UserSecurityQuestion;
use Exception;
use Illuminate\Support\Facades\Mail;

class ThreeFactorAuthService
{
    /**
     * Configuration for 3FA
     */
    protected array $config = [
        'otp_length' => 6,
        'otp_ttl' => 600, // 10 minutes in seconds
        'otp_max_attempts' => 5,
        'security_question_count' => 3,
        'security_answer_attempts' => 3,
        'biometric_failure_threshold' => 5,
        'sms_provider' => 'twilio', // or 'sns', 'nexmo'
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = array_merge($this->config, config('three_factor_auth', []));
    }

    /**
     * Check if 3FA is required for user
     */
    public function isThreeFactorRequired(User $user, array $context = []): bool
    {
        // Check if 3FA is enabled globally or for user
        $otpSetting = OtpSetting::where('user_id', $user->id)->first();
        if (! $otpSetting || ! $otpSetting->otp_enabled) {
            return false;
        }

        // Check if this is a suspicious login
        if (! empty($context['is_suspicious'])) {
            return true;
        }

        // Check if new device/location
        if (! empty($context['is_new_device']) || ! empty($context['is_new_location'])) {
            return true;
        }

        // Check if user has completed 3FA before
        $recentLog = ThreeFactorAuthLog::forUser($user->id)
            ->successful()
            ->recent()
            ->latest()
            ->first();

        // If no successful 3FA in last 24 hours, require it
        if (! $recentLog) {
            return true;
        }

        return false;
    }

    /**
     * Get required authentication factors for user
     */
    public function getRequiredFactors(User $user): array
    {
        try {
            $factors = [];

            // Check OTP requirement
            $otpSetting = OtpSetting::where('user_id', $user->id)->first();
            if ($otpSetting && $otpSetting->otp_enabled) {
                $factors[] = 'otp';
            }

            // Check Security Questions requirement
            $securityQuestionsCount = UserSecurityQuestion::where('user_id', $user->id)
                ->whereNotNull('answer_hash')
                ->count();
            if ($securityQuestionsCount >= 2) {
                $factors[] = 'security_question';
            }

            // Check Biometric requirement
            $biometricsCount = BiometricData::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('is_verified', true)
                ->count();
            if ($biometricsCount > 0) {
                $factors[] = 'biometric';
            }

            // If no factors available, require OTP at minimum
            if (empty($factors)) {
                $factors[] = 'otp';
            }

            return $factors;
        } catch (Exception $e) {
            \Log::error('Error getting required factors: ' . $e->getMessage());
            // Default to OTP if error
            return ['otp'];
        }
    }

    /**
     * Get authentication status for user
     */
    public function getAuthenticationStatus(User $user): array
    {
        return [
            'requires_otp' => $this->requiresOtp($user),
            'requires_security_questions' => $this->requiresSecurityQuestions($user),
            'requires_biometric' => $this->requiresBiometric($user),
            'otp_verified' => $this->isOtpVerified($user),
            'security_questions_verified' => $this->areSecurityQuestionsVerified($user),
            'biometric_verified' => $this->isBiometricVerified($user),
            'all_factors_verified' => $this->areAllFactorsVerified($user),
        ];
    }

    /**
     * Check if OTP is required
     */
    public function requiresOtp(User $user): bool
    {
        $otpSetting = OtpSetting::where('user_id', $user->id)->first();

        return $otpSetting && $otpSetting->otp_enabled;
    }

    /**
     * Check if security questions are required
     */
    public function requiresSecurityQuestions(User $user): bool
    {
        try {
            $count = UserSecurityQuestion::where('user_id', $user->id)
                ->whereNotNull('answer_hash')
                ->count();
            return $count >= 2;
        } catch (Exception $e) {
            \Log::error('Error checking security questions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if biometric is required
     */
    public function requiresBiometric(User $user): bool
    {
        try {
            $count = BiometricData::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('is_verified', true)
                ->count();
            return $count > 0;
        } catch (Exception $e) {
            \Log::error('Error checking biometric: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ==================== OTP METHODS ====================
     */

    /**
     * Generate random OTP
     */
    protected function generateOtp(): string
    {
        return str_pad(random_int(0, pow(10, $this->config['otp_length']) - 1),
            $this->config['otp_length'], '0', STR_PAD_LEFT);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(User $user, string $otp, ?string $ipAddress = null): array
    {
        try {
            $otpSetting = OtpSetting::where('user_id', $user->id)->first();

            if (! $otpSetting) {
                $this->logAuthAttempt($user->id, 'otp', 'failed', $ipAddress, 'No OTP setting found');

                return ['success' => false, 'message' => 'No OTP setting found', 'next_step' => null];
            }

            // Check if OTP is expired
            if ($otpSetting->isOtpExpired()) {
                $this->logAuthAttempt($user->id, 'otp', 'failed', $ipAddress, 'OTP expired');

                return ['success' => false, 'message' => 'OTP expired', 'next_step' => null];
            }

            // Check if attempts exceeded
            if ($otpSetting->hasExceededAttempts($this->config['otp_max_attempts'])) {
                $this->logAuthAttempt($user->id, 'otp', 'failed', $ipAddress, 'Exceeded max attempts');

                return ['success' => false, 'message' => 'Exceeded max attempts', 'next_step' => null];
            }

            // Check if OTP code is set
            if (! $otpSetting->otp_code) {
                $this->logAuthAttempt($user->id, 'otp', 'failed', $ipAddress, 'No OTP code found');

                return ['success' => false, 'message' => 'No OTP code found. Please request a new OTP.', 'next_step' => null];
            }

            // Verify OTP
            if (! hash_equals(hash('sha256', $otp), $otpSetting->otp_code)) {
                $otpSetting->incrementOtpAttempts();
                $this->logAuthAttempt($user->id, 'otp', 'failed', $ipAddress, 'Invalid OTP');

                return ['success' => false, 'message' => 'Invalid OTP', 'next_step' => null];
            }

            // OTP verified successfully
            $otpSetting->resetOtp();
            $this->logAuthAttempt($user->id, 'otp', 'success', $ipAddress);

            // Check for next steps
            $nextStep = $this->requiresSecurityQuestions($user) ? 'security_question' : null;

            return ['success' => true, 'message' => 'OTP verified', 'next_step' => $nextStep];
        } catch (Exception $e) {
            \Log::error("Error verifying OTP for user {$user->id}: ".$e->getMessage());

            return ['success' => false, 'message' => 'Verification failed', 'next_step' => null];
        }
    }

    /**
     * Send OTP via email
     */
    protected function sendOtpViaEmail(string $email, string $otp, string $userName = ''): void
    {
        try {
            Mail::send('emails.otp', [
                'otp' => $otp,
                'userName' => $userName,
                'validMinutes' => intval($this->config['otp_ttl'] / 60),
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Mã OTP xác thực 3FA - '.config('app.name'));
            });
        } catch (Exception $e) {
            \Log::error("Failed to send OTP email to {$email}: ".$e->getMessage());
        }
    }

    /**
     * Send OTP via SMS
     */
    protected function sendOtpViaSms(string $phoneNumber, string $otp): void
    {
        try {
            $message = "Mã xác thực 3FA của bạn là: $otp (Hết hạn trong 10 phút)";

            // Integrate with SMS provider (Twilio, AWS SNS, Nexmo, etc.)
            // For now, just log it
            \Log::info("SMS OTP sent to {$phoneNumber}: {$otp}");
        } catch (Exception $e) {
            \Log::error("Failed to send OTP SMS to {$phoneNumber}: ".$e->getMessage());
        }
    }

    /**
     * Check if OTP is verified
     */
    public function isOtpVerified(User $user): bool
    {
        $log = ThreeFactorAuthLog::forUser($user->id)
            ->byMethod('otp')
            ->successful()
            ->latest()
            ->first();

        if (! $log || ! $log->verified_at) {
            return false;
        }

        // Check if verification is recent (within last hour)
        return $log->verified_at->greaterThan(now()->subHour());
    }

    /**
     * ==================== SECURITY QUESTIONS METHODS ====================
     */

    /**
     * Setup security questions for user
     */
    public function setupSecurityQuestions(User $user, array $questionAnswers): bool
    {
        try {
            // Get least used questions
            $questions = SecurityQuestion::active()
                ->leastUsed($this->config['security_question_count'])
                ->get();

            if ($questions->count() < $this->config['security_question_count']) {
                \Log::error('Not enough security questions available');

                return false;
            }

            // Remove existing answers
            UserSecurityQuestion::forUser($user->id)->delete();

            // Add new answers
            foreach ($questions as $question) {
                if (isset($questionAnswers[$question->id])) {
                    UserSecurityQuestion::create([
                        'user_id' => $user->id,
                        'security_question_id' => $question->id,
                        'answer_hash' => hash('sha256', strtolower(trim($questionAnswers[$question->id]))),
                    ]);
                    $question->incrementUsage();
                }
            }

            return true;
        } catch (Exception $e) {
            \Log::error("Error setting up security questions for user {$user->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get random security questions for user
     */
    public function getRandomSecurityQuestions(User $user, ?int $count = null): array
    {
        $count = $count ?? $this->config['security_question_count'];

        $questions = UserSecurityQuestion::forUser($user->id)
            ->active()
            ->inRandomOrder()
            ->limit($count)
            ->with('question')
            ->get();

        return $questions->map(function ($uq) {
            return [
                'id' => $uq->id,
                'question' => $uq->question->question ?? '',
                'question_id' => $uq->security_question_id,
                'description' => $uq->question->description ?? '',
            ];
        })->toArray();
    }

    /**
     * Verify security questions answers
     */
    public function verifySecurityAnswers(User $user, array $answers, ?string $ipAddress = null): array
    {
        try {
            $userQuestions = UserSecurityQuestion::forUser($user->id)
                ->active()
                ->get();

            if ($userQuestions->isEmpty()) {
                $this->logAuthAttempt($user->id, 'security_question', 'failed', $ipAddress, 'No questions set');

                return ['success' => false, 'message' => 'No questions set'];
            }

            $correctAnswers = 0;
            /** @var UserSecurityQuestion $userQuestion */
            foreach ($userQuestions as $userQuestion) {
                if (isset($answers[$userQuestion->id])) {
                    if ($userQuestion->verifyAnswer($answers[$userQuestion->id])) {
                        $correctAnswers++;
                    } else {
                        $userQuestion->incrementVerificationAttempts();

                        if ($userQuestion->hasExceededAttempts($this->config['security_answer_attempts'])) {
                            $userQuestion->deactivate();
                        }
                    }
                }
            }

            // Require all questions answered correctly
            if ($correctAnswers === $userQuestions->count()) {
                /** @var UserSecurityQuestion $userQuestion */
                foreach ($userQuestions as $userQuestion) {
                    $userQuestion->recordSuccessfulVerification();
                }
                $this->logAuthAttempt($user->id, 'security_question', 'success', $ipAddress);

                return ['success' => true, 'message' => 'Security questions verified'];
            }

            $this->logAuthAttempt($user->id, 'security_question', 'failed', $ipAddress, 'Incorrect answers');

            return ['success' => false, 'message' => 'Incorrect answers'];
        } catch (Exception $e) {
            \Log::error("Error verifying security answers for user {$user->id}: ".$e->getMessage());

            return ['success' => false, 'message' => 'Verification failed'];
        }
    }

    /**
     * Check if security questions are verified
     */
    public function areSecurityQuestionsVerified(User $user): bool
    {
        $userQuestions = UserSecurityQuestion::forUser($user->id)
            ->active()
            ->count();

        if ($userQuestions === 0) {
            return false;
        }

        $verified = UserSecurityQuestion::forUser($user->id)
            ->active()
            ->where('last_verified_at', '>=', now()->subHour())
            ->count();

        return $verified === $userQuestions;
    }

    /**
     * ==================== BIOMETRIC METHODS ====================
     */

    /**
     * Store biometric data
     */
    public function storeBiometricData(
        User $user,
        string $biometricType,
        string $biometricData,
        string $deviceId,
        string $deviceName = 'Unknown'
    ): ?BiometricData {
        try {
            $biometric = BiometricData::create([
                'user_id' => $user->id,
                'biometric_type' => $biometricType,
                'biometric_data_encrypted' => encrypt($biometricData),
                'device_id' => $deviceId,
                'device_name' => $deviceName,
                'is_verified' => false,
                'is_active' => true,
            ]);

            \Log::info("Biometric data stored for user {$user->id}: {$biometricType} on {$deviceName}");

            return $biometric;
        } catch (Exception $e) {
            \Log::error("Error storing biometric data for user {$user->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Verify biometric data
     */
    public function verifyBiometricData(
        User $user,
        string $biometricType,
        string $biometricData,
        ?string $ipAddress = null
    ): array {
        try {
            $biometric = BiometricData::forUser($user->id)
                ->ofType($biometricType)
                ->active()
                ->verified()
                ->first();

            if (! $biometric) {
                $this->logAuthAttempt($user->id, 'biometric', 'failed', $ipAddress, 'No verified biometric found');

                return ['success' => false, 'message' => 'No verified biometric found'];
            }

            // In real implementation, compare biometric data using ML/AI service
            // For now, we'll do a simple comparison
            $storedData = $biometric->decryptBiometricData();

            // Use similarity matching (in production, use specialized biometric matching algorithms)
            $similarity = $this->calculateBiometricSimilarity($biometricData, $storedData);

            if ($similarity >= 0.95) { // 95% similarity threshold
                $biometric->recordSuccessfulVerification();
                $this->logAuthAttempt($user->id, 'biometric', 'success', $ipAddress);

                return ['success' => true, 'message' => 'Biometric verified'];
            } else {
                $biometric->recordFailedVerification();

                if ($biometric->hasTooManyFailures($this->config['biometric_failure_threshold'])) {
                    $biometric->deactivate();
                    \Log::warning("Biometric deactivated for user {$user->id} due to too many failures");
                }

                $this->logAuthAttempt($user->id, 'biometric', 'failed', $ipAddress, 'Biometric mismatch');

                return ['success' => false, 'message' => 'Biometric mismatch'];
            }
        } catch (Exception $e) {
            \Log::error("Error verifying biometric for user {$user->id}: ".$e->getMessage());

            return ['success' => false, 'message' => 'Verification failed'];
        }
    }

    /**
     * Calculate biometric similarity (simple implementation)
     */
    protected function calculateBiometricSimilarity(string $data1, string $data2): float
    {
        // In production, use specialized biometric ML libraries
        // This is a placeholder implementation
        $similarity = similar_text($data1, $data2);

        return min(1.0, $similarity / max(strlen($data1), strlen($data2)));
    }

    /**
     * Get user biometrics
     */
    public function getUserBiometrics(User $user): array
    {
        $biometrics = BiometricData::forUser($user->id)
            ->active()
            ->get();

        return $biometrics->map(function ($bio) {
            // Calculate success rate safely
            $successRate = 0;
            if (method_exists($bio, 'getSuccessRate')) {
                $successRate = $bio->getSuccessRate();
            } elseif (isset($bio->verification_success_count) && isset($bio->verification_fail_count)) {
                $total = $bio->verification_success_count + $bio->verification_fail_count;
                $successRate = $total > 0 ? round(($bio->verification_success_count / $total) * 100, 2) : 0;
            }

            return [
                'id' => $bio->id,
                'type' => $bio->biometric_type,
                'device_name' => $bio->device_name,
                'is_verified' => $bio->is_verified,
                'success_rate' => $successRate,
                'last_verified' => isset($bio->last_verified_at) ? $bio->last_verified_at?->diffForHumans() : null,
            ];
        })->toArray();
    }

    /**
     * Check if biometric is verified
     */
    public function isBiometricVerified(User $user): bool
    {
        $log = ThreeFactorAuthLog::forUser($user->id)
            ->byMethod('biometric')
            ->successful()
            ->latest()
            ->first();

        if (! $log || ! $log->verified_at) {
            return false;
        }

        return $log->verified_at->greaterThan(now()->subHour());
    }

    /**
     * ==================== GENERAL METHODS ====================
     */

    /**
     * Check if all required factors are verified
     */
    public function areAllFactorsVerified(User $user): bool
    {
        $status = $this->getAuthenticationStatus($user);

        $allVerified = true;
        foreach ($status as $key => $value) {
            if (str_starts_with($key, 'requires_') && $status[str_replace('requires_', '', $key).'_verified'] === false && $value === true) {
                $allVerified = false;
                break;
            }
        }

        return $allVerified;
    }

    /**
     * Reset 3FA for user
     */
    public function resetThreeFactorAuth(User $user): bool
    {
        try {
            OtpSetting::where('user_id', $user->id)->delete();
            UserSecurityQuestion::where('user_id', $user->id)->delete();
            BiometricData::where('user_id', $user->id)->delete();

            \Log::info("3FA reset for user {$user->id}");

            return true;
        } catch (Exception $e) {
            \Log::error("Error resetting 3FA for user {$user->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Log authentication attempt
     */
    protected function logAuthAttempt(
        int $userId,
        string $method,
        string $status,
        ?string $ipAddress = null,
        ?string $failureReason = null
    ): void {
        try {
            ThreeFactorAuthLog::create([
                'user_id' => $userId,
                'auth_method' => $method,
                'status' => $status,
                'ip_address' => $ipAddress ?? request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_info' => $this->getDeviceInfo(),
                'failure_reason' => $failureReason,
                'attempt_number' => 1,
            ]);
        } catch (Exception $e) {
            \Log::error('Error logging auth attempt: '.$e->getMessage());
        }
    }

    /**
     * Get device information
     */
    protected function getDeviceInfo(): string
    {
        $agent = request()->userAgent();

        // Parse user agent to get device info
        return substr($agent ?? 'Unknown', 0, 255);
    }

    /**
     * Get 3FA statistics
     */
    public function getThreeFactorAuthStatistics(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_attempts' => ThreeFactorAuthLog::where('created_at', '>=', $startDate)->count(),
            'successful_attempts' => ThreeFactorAuthLog::where('created_at', '>=', $startDate)
                ->successful()
                ->count(),
            'failed_attempts' => ThreeFactorAuthLog::where('created_at', '>=', $startDate)
                ->failed()
                ->count(),
            'by_method' => [
                'otp' => ThreeFactorAuthLog::where('created_at', '>=', $startDate)
                    ->byMethod('otp')
                    ->count(),
                'security_question' => ThreeFactorAuthLog::where('created_at', '>=', $startDate)
                    ->byMethod('security_question')
                    ->count(),
                'biometric' => ThreeFactorAuthLog::where('created_at', '>=', $startDate)
                    ->byMethod('biometric')
                    ->count(),
            ],
            'success_rate' => round(
                (ThreeFactorAuthLog::where('created_at', '>=', $startDate)->successful()->count() /
                max(ThreeFactorAuthLog::where('created_at', '>=', $startDate)->count(), 1)) * 100,
                2
            ),
        ];
    }

    /**
     * Cleanup old 3FA logs
     */
    public function cleanupOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return ThreeFactorAuthLog::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Mark 3FA as verified for user
     */
    public function markAsVerified(User $user): bool
    {
        try {
            ThreeFactorAuthLog::create([
                'user_id' => $user->id,
                'auth_method' => 'combined',
                'status' => 'success',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_info' => $this->getDeviceInfo(),
                'verified_at' => now(),
            ]);

            return true;
        } catch (Exception $e) {
            \Log::error('Error marking 3FA as verified: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get security question for user
     */
    public function getSecurityQuestion(User $user): ?array
    {
        $questions = $this->getRandomSecurityQuestions($user, 1);

        return count($questions) > 0 ? $questions[0] : null;
    }

    /**
     * Verify a single security question
     */
    public function verifySecurityQuestion(User $user, int $questionId, string $answer): array
    {
        try {
            $userQuestion = UserSecurityQuestion::find($questionId);

            if (! $userQuestion || $userQuestion->user_id !== $user->id) {
                return ['success' => false, 'message' => 'Question not found'];
            }

            if ($userQuestion->verifyAnswer($answer)) {
                $userQuestion->recordSuccessfulVerification();

                return ['success' => true, 'message' => 'Answer correct'];
            } else {
                $userQuestion->incrementVerificationAttempts();
                if ($userQuestion->hasExceededAttempts($this->config['security_answer_attempts'])) {
                    $userQuestion->deactivate();
                }

                return ['success' => false, 'message' => 'Answer incorrect'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Verification failed'];
        }
    }

    /**
     * Get all security questions
     */
    public function getAllSecurityQuestions(): array
    {
        $questions = SecurityQuestion::active()->get();

        return $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
            ];
        })->toArray();
    }

    /**
     * Resend OTP to user
     */
    public function resendOTP(User $user): array
    {
        try {
            $result = $this->generateAndSendOtp($user);
            if ($result) {
                return ['success' => true, 'message' => 'OTP sent successfully'];
            }

            return ['success' => false, 'message' => 'Failed to send OTP'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error sending OTP'];
        }
    }

    /**
     * Cancel 3FA for user
     */
    public function cancelThreeFactorAuth(User $user): bool
    {
        try {
            // Clear any pending 3FA sessions
            session()->forget('3fa_pending');
            session()->forget('3fa_user_id');
            \Log::info("3FA cancelled for user {$user->id}");

            return true;
        } catch (Exception $e) {
            \Log::error('Error cancelling 3FA: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Invalidate session for user
     */
    public function invalidateSession(User $user): bool
    {
        try {
            session()->invalidate();
            session()->regenerateToken();
            \Log::info("Session invalidated for user {$user->id}");

            return true;
        } catch (Exception $e) {
            \Log::error('Error invalidating session: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Check if 3FA verification is required for user
     */
    public function isVerificationRequired(User $user): bool
    {
        // Check if verification is already cached in session
        $verified_at = session()->get('3fa_verified_at');

        if (! $verified_at) {
            // No verification timestamp in session - needs verification
            // Just check if any factor is required
            $requiredFactors = $this->getRequiredFactors($user);
            return ! empty($requiredFactors);
        }

        // Check if verification is still recent (within 1 hour)
        return \Carbon\Carbon::parse($verified_at)->diffInMinutes(now()) > 60;
    }

    /**
     * Mark 3FA session as verified
     */
    public function markSessionVerified(User $user): void
    {
        session()->put('3fa_verified_at', now());
        session()->put('3fa_verified_user_id', $user->id);
    }

    /**
     * Generate and send OTP - return type should be array like others
     */
    public function generateAndSendOTP(User $user, string $method = 'email'): array
    {
        try {
            \Log::info("Starting OTP generation for user {$user->id}");
            
            $otpSetting = OtpSetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'otp_delivery_method' => $method,
                    'otp_enabled' => true,
                ]
            );

            \Log::info("OtpSetting found/created for user {$user->id}");

            // Check rate limiting
            if (! $otpSetting->canSendOtp()) {
                \Log::warning("OTP rate limit exceeded for user {$user->id}");

                return ['success' => false, 'message' => 'Rate limit exceeded'];
            }

            // Generate OTP
            $otp = $this->generateOtp();
            $expiresAt = now()->addSeconds($this->config['otp_ttl']);

            \Log::info("Generated OTP: {$otp}, Expires at: {$expiresAt}");

            // Update with OTP code
            $updateResult = $otpSetting->update([
                'otp_code' => hash('sha256', $otp),
                'otp_expires_at' => $expiresAt,
                'otp_attempts' => 0,
                'last_otp_sent_at' => now(),
            ]);

            \Log::info("OTP update result: " . ($updateResult ? 'success' : 'failed'));

            // Store plain OTP in cache temporarily for development/debugging (expires in 10 minutes)
            if (app()->isLocal()) {
                \Cache::put("otp_debug_{$user->id}", $otp, now()->addMinutes(10));
                \Log::info("OTP cached for debugging: {$otp}");
            }

            // Send OTP
            if ($method === 'sms' && $otpSetting->phone_number) {
                $this->sendOtpViaSms($otpSetting->phone_number, $otp);
            } else {
                $this->sendOtpViaEmail($user->email, $otp, $user->name);
            }

            \Log::info("OTP sent successfully to user {$user->id}");

            return ['success' => true, 'message' => 'OTP sent successfully'];
        } catch (Exception $e) {
            \Log::error("Failed to generate/send OTP for user {$user->id}: " . $e->getMessage());
            \Log::error("Exception trace: " . $e->getTraceAsString());

            return ['success' => false, 'message' => 'Failed to send OTP'];
        }
    }
}
