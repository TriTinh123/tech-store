<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OtpService;
use Illuminate\Http\Request;

/**
 * PurchaseRiskController
 * ======================
 * Handles the OTP challenge step that is triggered when
 * PurchaseFraudService flags an order as high-risk.
 *
 * Flow:
 *  CheckoutController::store()
 *    → fraud detected → store pending data in session → send OTP → redirect here
 *
 *  GET /checkout/verify-purchase  → show()   (OTP form)
 *  POST /checkout/verify-purchase → verify() (check OTP → create order)
 *  POST /checkout/verify-purchase/resend → resend() (re-send OTP)
 */
class PurchaseRiskController extends Controller
{
    /** GET /checkout/verify-purchase — show OTP challenge page */
    public function show()
    {
        $fraud = session('purchase.fraud');
        $total = session('purchase.total');

        if (! $fraud || ! $total) {
            return redirect()->route('checkout.show')
                ->with('error', 'No orders pending verification.');
        }

        $user = auth()->user();

        return view('purchase-risk-challenge', compact('fraud', 'total', 'user'));
    }

    /** POST /checkout/verify-purchase — verify OTP then create the order */
    public function verify(Request $request)
    {
        $fraud     = session('purchase.fraud');
        $validated = session('purchase.pending_checkout');
        $cart      = session('purchase.cart');
        $total     = session('purchase.total');

        if (! $fraud || ! $validated || ! $cart || ! $total) {
            return redirect()->route('checkout.show')
                ->with('error', 'Verification session has expired. Please place a new order.');
        }

        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if (! app(OtpService::class)->verify($user, $request->otp_code)) {
            return back()->withErrors([
                'otp_code' => 'Invalid or expired OTP code.',
            ]);
        }

        // OTP verified → create the order
        $order = $this->buildOrder($validated, $cart, $total);

        // Clear all purchase session keys + cart
        session()->forget([
            'purchase.pending_checkout',
            'purchase.cart',
            'purchase.total',
            'purchase.fraud',
            'cart',
        ]);

        return redirect()->route('checkout.payment.method', $order)
            ->with('success', '✅ Order verified successfully! Please select a payment method.');
    }

    /** POST /checkout/verify-purchase/resend — send a fresh OTP */
    public function resend()
    {
        if (! session('purchase.fraud')) {
            return redirect()->route('checkout.show');
        }

        app(OtpService::class)->send(auth()->user());

        return back()->with('info', '📧 A new OTP code has been sent to your email.');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function buildOrder(array $validated, array $cart, float $total): Order
    {
        $orderNumber = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);

        $order = Order::create([
            'user_id'          => auth()->id(),
            'order_number'     => $orderNumber,
            'customer_name'    => $validated['customer_name'],
            'customer_email'   => $validated['customer_email'],
            'customer_phone'   => $validated['customer_phone'],
            'delivery_address' => $validated['delivery_address'],
            'total_amount'     => $total,
            'status'           => 'pending',
            'notes'            => $validated['notes'] ?? null,
        ]);

        foreach ($cart as $id => $quantity) {
            $product = Product::find($id);
            if ($product) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_price' => $product->price,
                    'quantity'      => $quantity,
                    'subtotal'      => $product->price * $quantity,
                ]);
            }
        }

        OrderCreated::dispatch($order);

        return $order;
    }
}
