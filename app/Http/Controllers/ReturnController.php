<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show form to create return request
     */
    public function create($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'Chỉ có thể yêu cầu hoàn trả với đơn hàng đã giao');
        }

        $items = $order->items;

        return view('returns.create', compact('order', 'items'));
    }

    /**
     * Store return request
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'reason' => 'required|in:defective,wrong_item,not_as_described,changed_mind,other',
            'description' => 'required|string|min:10|max:500',
        ]);

        $item = OrderItem::findOrFail($validated['order_item_id']);

        if ($item->order_id !== $order->id) {
            abort(403, 'Invalid item');
        }

        ReturnRequest::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'refund_amount' => $item->subtotal,
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Yêu cầu hoàn trả đã được gửi. Chúng tôi sẽ xem xét và liên hệ bạn sớm.');
    }

    /**
     * View return requests
     */
    public function index()
    {
        $returns = ReturnRequest::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->with('order', 'orderItem')->paginate(10);

        return view('returns.index', compact('returns'));
    }

    /**
     * View return detail
     */
    public function show($id)
    {
        $return = ReturnRequest::findOrFail($id);

        if ($return->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('returns.show', compact('return'));
    }
}
