<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // View account page
    public function show()
    {
        $user = auth()->user();

        return view('profile.show', compact('user'));
    }

    // Edit personal information
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated!');
    }

    // Change password page
    public function editPassword()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password changed!');
    }

    // Update security question
    public function updateSecurityQuestion(Request $request)
    {
        $request->validate([
            'security_question' => 'required|string|max:255',
            'security_answer'   => 'required|string|min:2|max:255',
        ]);

        auth()->user()->update([
            'security_question' => $request->security_question,
            'security_answer'   => Hash::make(strtolower(trim($request->security_answer))),
        ]);

        return redirect()->route('profile.show')->with('success', 'Security question updated successfully!');
    }

    // View order history
    public function orderHistory()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.order-history', compact('orders'));
    }

    // View order details
    public function orderDetail($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $items = $order->items()->with('product')->get();

        return view('profile.order-detail', compact('order', 'items'));
    }
}
