<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BankTransferPaymentService;
use App\Services\PayPalPaymentService;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function chooseMethod(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.choose-method', compact('order'));
    }

    public function process(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $paymentMethod = $request->input('payment_method') ?? $order->payment_method;

        if (! $paymentMethod) {
            return back()->with('error', 'Vui lòng chọn phương thức thanh toán');
        }

        // Update order payment method
        $order->update(['payment_method' => $paymentMethod]);

        return match ($paymentMethod) {
            'stripe' => $this->processStripe($order),
            'paypal' => $this->processPayPal($order),
            'bank_transfer' => $this->processBankTransfer($order),
            'cod' => $this->processCOD($order),
            default => back()->with('error', 'Invalid payment method'),
        };
    }

    private function processStripe(Order $order)
    {
        $service = new StripePaymentService($order);
        $session = $service->initiate();

        return redirect($session->url);
    }

    private function processPayPal(Order $order)
    {
        $service = new PayPalPaymentService($order);
        $paypalOrder = $service->initiate();

        $approvalLink = collect($paypalOrder->links)->firstWhere('rel', 'approve')->href;

        return redirect($approvalLink);
    }

    private function processBankTransfer(Order $order)
    {
        $service = new BankTransferPaymentService($order);
        $paymentData = $service->initiate();

        return view('payment.bank-transfer', compact('order', 'paymentData'));
    }

    private function processCOD(Order $order)
    {
        $order->update([
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        return redirect()->route('payment.success', $order)
            ->with('success', 'Đơn hàng đã được tạo. Vui lòng thanh toán khi nhận hàng.');
    }

    public function confirmBankTransfer(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Confirm bank transfer payment
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'paid_at' => now(),
        ]);

        return redirect()->route('payment.success', $order)
            ->with('success', 'Cảm ơn bạn! Thanh toán đã được xác nhận.');
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Handle Stripe webhook callback
        if ($order->payment_gateway === 'stripe' && request('session_id')) {
            $service = new StripePaymentService($order);
            $service->verify(request('session_id'));
        }

        // Handle PayPal callback
        if ($order->payment_gateway === 'paypal' && request('token')) {
            $service = new PayPalPaymentService($order);
            $service->verify(request('token'));
        }

        $order->load('items');

        return view('payment.success', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->update(['status' => 'cancelled']);

        return view('payment.cancel', compact('order'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'transaction_ref' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // For manual bank transfer verification
        if ($order->payment_method === 'bank_transfer') {
            $order->update([
                'payment_status' => 'pending_verification',
                'payment_reference' => json_encode([
                    'transaction_ref' => $request->transaction_ref,
                    'verified_at' => now(),
                ]),
            ]);

            return back()->with('success', 'Yêu cầu xác minh thanh toán đã được gửi. Admin sẽ xác minh trong vòng 24 giờ.');
        }

        return back()->with('error', 'Không hợp lệ');
    }
}
