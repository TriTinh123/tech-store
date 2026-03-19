<?php

namespace App\Http\Controllers;

use App\Models\OrderReturn;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderReturnController extends Controller
{
    /** Show create return request form */
    public function create($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Only delivered orders can be returned
        if (!in_array($order->status, ['delivered', 'completed'])) {
            return back()->with('error', 'Only delivered orders can be returned.');
        }

        $existing = OrderReturn::where('order_id', $order->id)->first();
        if ($existing) {
            return back()->with('error', 'This order already has a return request.');
        }

        return view('returns.create', compact('order'));
    }

    /** Store return request */
    public function store(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($order->status, ['delivered', 'completed'])) {
            return back()->with('error', 'Only delivered orders can be returned.');
        }

        $validated = $request->validate([
            'reason'      => 'required|string|min:10|max:1000',
            'return_type' => 'required|in:refund,exchange',
        ]);

        OrderReturn::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'reason'      => $validated['reason'],
            'return_type' => $validated['return_type'],
            'status'      => 'pending',
        ]);

        return redirect()->route('orders.show', $orderId)
            ->with('success', 'Return request submitted. We will review and respond as soon as possible.');
    }
}
