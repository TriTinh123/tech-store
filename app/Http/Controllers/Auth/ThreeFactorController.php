<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\FaceAlertMail;
use App\Mail\ThreeFactorConfirmMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ThreeFactorController extends Controller
{
    /** Euclidean distance between two face descriptors (128-float arrays). */
    private function faceDistance(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            return 1.0; // treat as mismatch
        }
        $sum = 0.0;
        foreach ($a as $i => $v) {
            $diff = (float) $v - (float) $b[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }

    /** GET /auth/3fa — show 3FA challenge with AI risk summary */
    public function show()
    {
        $userId   = session('auth.pending_user_id');
        $riskData = session('auth.risk_data');

        if (! $userId || ! $riskData) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        return view('auth.3fa-challenge', compact('user', 'riskData'));
    }

    /** POST /auth/3fa — verify the third factor */
    public function verify(Request $request)
    {
        $userId   = session('auth.pending_user_id');
        $riskData = session('auth.risk_data');

        if (! $userId || ! $riskData) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        $method = $request->input('method', 'security_question');

        // ── Security Question ─────────────────────────────────────────────
        if ($method === 'security_question') {
            $request->validate([
                'security_answer' => 'required|string|min:1',
            ]);

            if (! $user->security_answer ||
                ! Hash::check(strtolower(trim($request->security_answer)), $user->security_answer)) {
                return back()->withErrors([
                    'security_answer' => 'Incorrect security answer. Please try again.',
                ])->withInput();
            }
        }
        // ── Biometric (face-api.js descriptor comparison) ─────────────────
        elseif ($method === 'biometric') {
            if (! $request->input('biometric_verified')) {
                return back()->withErrors([
                    'biometric' => 'Biometric authentication failed. No face was detected.',
                ]);
            }

            // Decode the sent descriptor
            $rawDescriptor = $request->input('face_descriptor');
            $sentDescriptor = is_string($rawDescriptor) ? json_decode($rawDescriptor, true) : null;

            if (! is_array($sentDescriptor) || count($sentDescriptor) < 64) {
                return back()->withErrors([
                    'biometric' => 'Face recognition failed — no valid face data was captured. Position your face clearly in the camera frame and try again, or use another verification method.',
                ]);
            }

            // Compare against enrolled descriptor
            $enrolled = $user->face_descriptor;

            if (! is_array($enrolled) || count($enrolled) < 64) {
                return back()->withErrors([
                    'biometric' => 'No face profile enrolled for this account. Use Security Question or Email Confirmation to complete login, then go to Profile → Security Settings to enroll your face.',
                ]);
            }

            $distance = $this->faceDistance($sentDescriptor, $enrolled);

            if ($distance > 0.55) {
                // Face does NOT match — send security alert email and block
                Mail::to($user->email)->send(new FaceAlertMail(
                    userName:    $user->name,
                    userEmail:   $user->email,
                    ipAddress:   $request->ip(),
                    userAgent:   $request->userAgent() ?? 'Unknown',
                    attemptedAt: now()->format('d/m/Y H:i:s') . ' (UTC+7)',
                ));

                return back()->withErrors([
                    'biometric' => 'Face not recognized. Access denied. A security alert has been sent to your email.',
                ]);
            }
        } else {
            return back()->withErrors(['method' => 'Invalid authentication method.']);
        }

        // ── 3FA passed → complete login ────────────────────────────────────
        return OtpController::completeLogin($request, $user, true);
    }

    /** POST /auth/3fa/email-send — send a signed confirmation link to the user's inbox */
    public function sendConfirmEmail(Request $request)
    {
        $userId   = session('auth.pending_user_id');
        $riskData = session('auth.risk_data');

        if (! $userId || ! $riskData) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // Generate a 15-minute signed URL — no extra DB column needed
        $confirmUrl = URL::temporarySignedRoute(
            'auth.3fa.email.confirm',
            now()->addMinutes(15),
            ['user' => $userId]
        );

        Mail::to($user->email)->send(new ThreeFactorConfirmMail(
            confirmUrl: $confirmUrl,
            userName:   $user->name,
            ipAddress:  $request->ip(),
            riskLevel:  $riskData['risk_level'] ?? 'high',
        ));

        return back()->with('email_confirm_sent', 'Confirmation email sent to ' . $user->email . '. Please check your inbox and click the link within 15 minutes.');
    }

    /** GET /auth/3fa/email-confirm?user=X&... (signed) — finalise login */
    public function emailConfirm(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Confirmation link is invalid or expired. Please log in again.');
        }

        $user = User::findOrFail($request->query('user'));

        return OtpController::completeLogin($request, $user, true);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Face enrollment (authenticated users only)
    // ─────────────────────────────────────────────────────────────────────────

    /** GET /auth/face-enroll */
    public function showEnrollFace()
    {
        return view('auth.face-enroll');
    }

    /** POST /auth/face-enroll — save face descriptor + snapshot for the authenticated user */
    public function enrollFace(Request $request)
    {
        $request->validate([
            'face_descriptor' => 'required|string',
        ]);

        $descriptor = json_decode($request->input('face_descriptor'), true);

        if (! is_array($descriptor) || count($descriptor) < 64) {
            return back()->withErrors(['face_descriptor' => 'Invalid face data. Please try again.']);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->face_descriptor = $descriptor;

        // Save face snapshot image
        $photoData = $request->input('face_photo');
        if ($photoData && str_starts_with($photoData, 'data:image/jpeg;base64,')) {
            $base64 = substr($photoData, strlen('data:image/jpeg;base64,'));
            $imageData = base64_decode($base64);
            if ($imageData !== false) {
                $dir = storage_path('app/public/face_photos');
                if (! is_dir($dir)) { mkdir($dir, 0755, true); }
                $filename = 'face_' . $user->id . '.jpg';
                file_put_contents($dir . '/' . $filename, $imageData);
                $user->face_photo = '/storage/face_photos/' . $filename;
            }
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Face profile enrolled successfully. Your face can now be used for biometric login.');
    }
}
