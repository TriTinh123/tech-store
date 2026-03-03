<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ThreeFactorAuthService;
use Illuminate\Http\Request;

class ThreeFactorController extends Controller
{
    protected $tfaService;

    public function __construct(ThreeFactorAuthService $tfaService)
    {
        $this->tfaService = $tfaService;
    }

    /**
     * Show OTP verification page
     */
    public function showVerifyOtp()
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check if user has pending 3FA
        $pending = $user->pendingThreeFactorAuth();
        if (! $pending) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-otp', [
            'user' => $user,
            'expiresAt' => $pending->otp_expires_at,
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Please enter the OTP code',
            'otp.digits' => 'OTP must be 6 digits',
        ]);

        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $result = $this->tfaService->verifyOtp($user, $request->otp);

        if (! $result['success']) {
            return back()->withErrors([
                'otp' => $result['message'],
            ])->withInput();
        }

        // OTP verified successfully
        if ($result['next_step'] === 'security_question') {
            return redirect()->route('3fa.verify-question');
        }

        // No security questions - mark as verified and redirect
        $this->tfaService->markAsVerified($user);

        // Redirect to admin if user is admin, otherwise to dashboard
        $redirectPath = $user->isAdmin() ? route('admin') : route('dashboard');

        return redirect($redirectPath)->with('success', '3FA verification successful!');
    }

    /**
     * Show security question verification page
     */
    public function showVerifyQuestion()
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check if user has pending 3FA
        $pending = $user->pendingThreeFactorAuth();
        if (! $pending) {
            return redirect()->route('dashboard');
        }

        // Get a random security question for the user
        $question = $this->tfaService->getSecurityQuestion($user);

        if (! $question) {
            return redirect()->back()->with('error', 'No security questions available');
        }

        return view('auth.verify-question', [
            'user' => $user,
            'question' => $question,
        ]);
    }

    /**
     * Verify security question answer
     */
    public function verifyQuestion(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:security_questions,id',
            'answer' => 'required|string|min:1',
        ], [
            'question_id.required' => 'Security question is missing',
            'question_id.exists' => 'Invalid security question',
            'answer.required' => 'Please enter your answer',
        ]);

        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $result = $this->tfaService->verifySecurityQuestion($user, $request->question_id, $request->answer);

        if (! $result['success']) {
            return back()->withErrors([
                'answer' => $result['message'],
            ])->with('question_id', $request->question_id);
        }

        // Security question verified - complete 3FA
        $this->tfaService->markAsVerified($user);

        // Redirect to admin if user is admin, otherwise to dashboard
        $redirectPath = $user->isAdmin() ? route('admin') : route('dashboard');

        return redirect($redirectPath)->with('success', '3FA verification completed successfully!');
    }

    /**
     * Show security questions setup page
     */
    public function showSetupQuestions()
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // If user already has security questions, show them
        $existingAnswers = $user->securityAnswers()->pluck('security_question_id')->toArray();

        // Get available questions (not already answered)
        $questions = collect($this->tfaService->getAllSecurityQuestions());

        // Get distinct questions for the form (at least 3)
        $distinctQuestions = $questions->filter(fn ($q) => ! in_array($q['id'], $existingAnswers))->take(3);

        if ($distinctQuestions->count() < 2) {
            return redirect()->back()->with('error', 'Not enough security questions available');
        }

        return view('auth.setup-questions', [
            'user' => $user,
            'questions' => $distinctQuestions,
            'hasExisting' => count($existingAnswers) > 0,
        ]);
    }

    /**
     * Save security questions setup
     */
    public function setupQuestions(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Validate that at least 2 questions are answered
        $rules = [];
        $messages = [];

        for ($i = 1; $i <= 3; $i++) {
            $rules["question_{$i}"] = 'required|exists:security_questions,id';
            $rules["answer_{$i}"] = 'required_with:question_'.$i.'|string|min:2';

            $messages["question_{$i}.required"] = "Question {$i} is required";
            $messages["question_{$i}.exists"] = "Invalid question {$i}";
            $messages["answer_{$i}.required_with"] = "Answer {$i} is required";
            $messages["answer_{$i}.min"] = 'Answer must be at least 2 characters';
        }

        $request->validate($rules, $messages);

        // Prepare answers array
        $answers = [];
        for ($i = 1; $i <= 3; $i++) {
            if ($request->has("question_{$i}") && $request->get("question_{$i}")) {
                $answers[$request->get("question_{$i}")] = $request->get("answer_{$i}");
            }
        }

        if (count($answers) < 2) {
            return back()->withErrors([
                'questions' => 'Please answer at least 2 security questions',
            ])->withInput();
        }

        // Save security questions
        $result = $this->tfaService->setupSecurityQuestions($user, $answers);

        return redirect()->route('3fa.setup-questions')->with('success', 'Security questions set successfully');
    }

    /**
     * Resend OTP code - fixed version
     */
    public function resendOtp(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Use the correct method name
        $result = $this->tfaService->resendOTP($user);

        if (! $result['success']) {
            return back()->withErrors([
                'otp' => $result['message'],
            ]);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Cancel 3FA verification (for testing purposes)
     */
    public function cancel()
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $this->tfaService->cancelThreeFactorAuth($user);
        $this->tfaService->invalidateSession($user);

        return redirect()->route('login')->with('info', '3FA verification cancelled');
    }
}
