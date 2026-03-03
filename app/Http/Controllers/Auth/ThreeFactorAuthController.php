<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityQuestion;
use App\Models\User;
use App\Services\ThreeFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ThreeFactorAuthController extends Controller
{
    protected ThreeFactorAuthService $threeFactorAuthService;

    public function __construct(ThreeFactorAuthService $threeFactorAuthService)
    {
        $this->threeFactorAuthService = $threeFactorAuthService;
    }

    /**
     * Show 3FA verification page
     */
    public function showVerification(Request $request)
    {
        $userId = $request->session()->get('pending_3fa_user_id');

        if (! $userId) {
            return redirect('/login')->with('error', 'Phiên xác thực không hợp lệ');
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect('/login')->with('error', 'Không tìm thấy người dùng');
        }

        // Get and cache required factors in session to avoid recalculating
        $factors = $request->session()->get('3fa_required_factors');
        if (! $factors) {
            $factors = $this->threeFactorAuthService->getRequiredFactors($user);
            $request->session()->put('3fa_required_factors', $factors);
        }

        // Show the first required factor
        if (in_array('otp', $factors)) {
            return $this->showOtpForm($request);
        } elseif (in_array('security_question', $factors)) {
            return $this->showSecurityQuestionsForm($request);
        } elseif (in_array('biometric', $factors)) {
            return $this->showBiometricForm($request);
        }

        return redirect('/login')->with('error', 'Không thể xác thực 3FA');
    }

    /**
     * Show OTP verification form
     */
    /**
     * Show OTP Form
     */
    public function showOtpForm(Request $request)
    {
        $userId = $request->session()->get('pending_3fa_user_id');

        if (! $userId) {
            return redirect('/login')->with('error', 'Phiên xác thực không hợp lệ');
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect('/login')->with('error', 'Không tìm thấy người dùng');
        }

        return view('auth.3fa.verify-otp', [
            'user' => $user,
        ]);
    }

    /**
     * Show Security Questions form
     */
    public function showSecurityQuestionsForm(Request $request)
    {
        $userId = $request->session()->get('pending_3fa_user_id');

        if (! $userId) {
            return redirect('/login')->with('error', 'Phiên xác thực không hợp lệ');
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect('/login')->with('error', 'Không tìm thấy người dùng');
        }

        $questions = $this->threeFactorAuthService->getRandomSecurityQuestions($user);

        return view('auth.3fa.verify-security-questions', [
            'user' => $user,
            'questions' => $questions,
        ]);
    }

    /**
     * Show Biometric verification form
     */
    public function showBiometricForm(Request $request)
    {
        $userId = $request->session()->get('pending_3fa_user_id');

        if (! $userId) {
            return redirect('/login')->with('error', 'Phiên xác thực không hợp lệ');
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect('/login')->with('error', 'Không tìm thấy người dùng');
        }

        $biometrics = $this->threeFactorAuthService->getUserBiometrics($user);

        return view('auth.3fa.verify-biometric', [
            'user' => $user,
            'biometrics' => $biometrics,
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP',
            'otp.numeric' => 'Mã OTP phải là số',
            'otp.digits' => 'Mã OTP phải có đúng 6 chữ số',
        ]);

        $userId = $request->session()->get('pending_3fa_user_id');
        $user = User::find($userId);

        if (! $user) {
            throw ValidationException::withMessages([
                'otp' => 'Phiên xác thực không hợp lệ',
            ]);
        }

        $ipAddress = $request->ip();

        $result = $this->threeFactorAuthService->verifyOtp($user, $request->otp, $ipAddress);

        if (! is_array($result) || ! $result['success']) {
            throw ValidationException::withMessages([
                'otp' => $result['message'] ?? 'Mã OTP không hợp lệ hoặc đã hết hạn',
            ]);
        }

        // OTP verified successfully - get cached required factors
        $factors = $request->session()->get('3fa_required_factors', []);

        // Check if there are more factors to verify
        $remainingFactors = [];
        foreach ($factors as $factor) {
            if ($factor !== 'otp') {
                $remainingFactors[] = $factor;
            }
        }

        if (! empty($remainingFactors)) {
            // Move to next factor
            if (in_array('security_question', $remainingFactors)) {
                return redirect()->route('3fa.security-questions');
            } elseif (in_array('biometric', $remainingFactors)) {
                return redirect()->route('3fa.biometric');
            }
        }

        // All factors verified
        return $this->handleVerificationSuccess($request, $user);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $userId = $request->session()->get('pending_3fa_user_id');
        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên xác thực không hợp lệ',
            ], 400);
        }

        if ($this->threeFactorAuthService->generateAndSendOtp($user)) {
            return response()->json([
                'success' => true,
                'message' => 'Mã OTP mới đã được gửi đến email của bạn',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể gửi mã OTP. Vui lòng thử lại sau',
        ], 400);
    }

    /**
     * Verify Security Questions
     */
    public function verifySecurityQuestions(Request $request)
    {
        $userId = $request->session()->get('pending_3fa_user_id');
        $user = User::find($userId);

        if (! $user) {
            throw ValidationException::withMessages([
                'answers' => 'Phiên xác thực không hợp lệ',
            ]);
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string|min:1',
        ], [
            'answers.required' => 'Vui lòng trả lời tất cả các câu hỏi',
            'answers.*.required' => 'Vui lòng trả lời tất cả các câu hỏi',
        ]);

        $ipAddress = $request->ip();
        $answers = [];

        // Convert answer IDs to format expected by service
        foreach ($request->answers as $id => $answer) {
            $answers[$id] = $answer;
        }

        $result = $this->threeFactorAuthService->verifySecurityAnswers($user, $answers, $ipAddress);

        if (! is_array($result) || ! $result['success']) {
            throw ValidationException::withMessages([
                'answers' => $result['message'] ?? 'Một hoặc nhiều câu trả lời không đúng',
            ]);
        }

        // Check if there are more factors - use cached factors
        $factors = $request->session()->get('3fa_required_factors', []);

        if (in_array('biometric', $factors) && ! $this->threeFactorAuthService->isBiometricVerified($user)) {
            return redirect()->route('3fa.biometric');
        }

        return $this->handleVerificationSuccess($request, $user);
    }

    /**
     * Verify Biometric
     */
    public function verifyBiometric(Request $request)
    {
        $request->validate([
            'biometric_data' => 'required|string',
        ], [
            'biometric_data.required' => 'Dữ liệu sinh trắc học không hợp lệ',
        ]);

        $userId = $request->session()->get('pending_3fa_user_id');
        $user = User::find($userId);

        if (! $user) {
            throw ValidationException::withMessages([
                'biometric_data' => 'Phiên xác thực không hợp lệ',
            ]);
        }

        $ipAddress = $request->ip();

        $result = $this->threeFactorAuthService->verifyBiometricData($user, 'fingerprint', $request->biometric_data, $ipAddress);

        if (! is_array($result) || ! $result['success']) {
            throw ValidationException::withMessages([
                'biometric_data' => $result['message'] ?? 'Xác thực sinh trắc học thất bại. Vui lòng thử lại',
            ]);
        }

        return $this->handleVerificationSuccess($request, $user);
    }

    /**
     * Handle successful verification
     */
    protected function handleVerificationSuccess(Request $request, User $user)
    {
        // Mark 3FA as verified in session
        $this->threeFactorAuthService->markSessionVerified($user);

        // Store user info temporarily
        $request->session()->put('verified_3fa_user_id', $user->id);
        $request->session()->put('verified_3fa_user_email', $user->email);

        // Clear pending 3FA session
        $request->session()->forget('pending_3fa_user_id');
        $request->session()->forget('pending_3fa_login');
        $request->session()->forget('3fa_required_factors');

        // Return to login page with success message
        return redirect('/login')->with('success', '✓ 3FA xác thực thành công. Vui lòng đăng nhập lại!');
    }

    /**
     * Get security questions list (for admin setup)
     */
    public function getSecurityQuestions()
    {
        $questions = SecurityQuestion::active()
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return response()->json([
            'questions' => $questions->map(fn ($q) => [
                'id' => $q->id,
                'question' => $q->question_vi,
            ]),
        ]);
    }

    /**
     * Setup security questions for user
     */
    public function setupSecurityQuestions(Request $request)
    {
        $request->validate([
            'answers' => 'required|array|min:3',
            'answers.*' => 'required|string|min:3',
        ], [
            'answers.required' => 'Vui lòng trả lời tất cả các câu hỏi',
            'answers.min' => 'Vui lòng trả lời ít nhất 3 câu hỏi',
        ]);

        if ($this->threeFactorAuthService->setupSecurityQuestions(auth()->user(), $request->answers)) {
            return redirect()->back()->with('success', 'Câu hỏi bảo mật đã được cài đặt');
        }

        throw ValidationException::withMessages([
            'answers' => 'Không thể cài đặt câu hỏi bảo mật',
        ]);
    }

    /**
     * Get 3FA status
     */
    public function getStatus(Request $request)
    {
        $user = auth()->user();
        $status = $this->threeFactorAuthService->getAuthenticationStatus($user);

        return response()->json(['status' => $status]);
    }

    /**
     * Reset 3FA for user
     */
    public function reset(Request $request)
    {
        $user = auth()->user();

        if ($this->threeFactorAuthService->resetThreeFactorAuth($user)) {
            return redirect()->back()->with('success', '3FA đã được đặt lại');
        }

        return redirect()->back()->with('error', 'Không thể đặt lại 3FA');
    }
}
