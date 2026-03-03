<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\PaymentGateway\PaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService;
    }

    public function show()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống');
        }

        $items = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = Product::find($id);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return view('checkout', compact('items', 'total', 'cart'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống');
        }

        // Calculate total
        $total = 0;
        foreach ($cart as $id => $quantity) {
            $product = Product::find($id);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }

        // Generate order number
        $orderNumber = 'ORD-'.date('YmdHis').'-'.rand(1000, 9999);

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => $orderNumber,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'delivery_address' => $validated['delivery_address'],
            'total_amount' => $total,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create order items
        foreach ($cart as $id => $quantity) {
            $product = Product::find($id);
            if ($product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ]);
            }
        }

        // Dispatch event for email notification
        OrderCreated::dispatch($order);

        // Clear cart
        session()->forget('cart');

        // Redirect to payment method selection
        return redirect()->route('payment.method', $order)->with('success', 'Đơn hàng đã được tạo. Vui lòng chọn phương thức thanh toán.');
    }

    /**
     * Show payment method selection page
     */
    public function showPaymentMethod(Order $order)
    {
        if ($order->payment_status && $order->payment_status !== 'pending') {
            return redirect()->route('order.success', $order)->with('info', 'Đơn hàng này đã thanh toán rồi');
        }

        $gateways = $this->paymentService->getAvailableGateways();

        return view('payment-method', compact('order', 'gateways'));
    }

    /**
     * Process payment with selected gateway
     */
    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_gateway' => 'required|in:cod,bank_transfer',
        ]);

        $gateway = $request->input('payment_gateway');

        // Map gateway names
        $gatewayMap = [
            'cod' => 'cod',
            'momo' => 'momo',
            'zalopay' => 'zalopay',
            'bank_transfer' => 'bank_transfer',
        ];

        $mappedGateway = $gatewayMap[$gateway] ?? $gateway;
        $paymentGateway = $this->paymentService->getGateway($mappedGateway);

        if (! $paymentGateway) {
            return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ');
        }

        // Process payment
        $result = $this->paymentService->processPayment($mappedGateway, $order);

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        // Check if requires redirect (Momo, Zalo Pay)
        if ($paymentGateway->requiresRedirect() && isset($result['payment_url'])) {
            return redirect($result['payment_url']);
        }

        // For COD: Go directly to success page
        if ($mappedGateway === 'cod') {
            $order->update(['payment_status' => 'completed', 'paid_at' => now()]);

            return redirect()->route('order.success', $order)->with('success', 'Đơn hàng đã được tạo thành công!');
        }

        // For Bank Transfer, show confirmation page
        return redirect()->route('payment.confirm', [
            'order' => $order,
            'gateway' => $mappedGateway,
        ])->with('success', $result['message']);
    }

    /**
     * Show payment confirmation page
     */
    public function confirmPayment(Order $order, $gateway)
    {
        $paymentGateway = $this->paymentService->getGateway($gateway);

        if (! $paymentGateway) {
            return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ');
        }

        $paymentDetails = $paymentGateway->getPaymentDetails($order);

        return view('payment-confirm', compact('order', 'gateway', 'paymentDetails'));
    }

    /**
     * Confirm transfer completion
     */
    public function confirmTransfer(Order $order)
    {
        // Mark payment as completed
        $order->update([
            'payment_status' => 'completed',
            'paid_at' => now(),
        ]);

        return redirect()->route('order.success', $order)->with('success', 'Cảm ơn bạn! Đơn hàng của bạn đã được xác nhận. Chúng tôi sẽ xác minh thanh toán và chuẩn bị hàng.');
    }

    /**
     * Handle payment callback from payment gateway
     */
    public function handleCallback(Request $request)
    {
        $gateway = $request->input('gateway');
        $reference = $request->input('reference');
        $status = $request->input('status');

        // Find order by payment reference
        $order = Order::where('payment_reference', $reference)->first();

        if (! $order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
        }

        // Update order based on callback
        if ($status === 'success' || $status === 'completed') {
            $order->update([
                'payment_status' => 'completed',
                'paid_at' => now(),
                'status' => 'confirmed',
            ]);

            return redirect()->route('order.success', $order)->with('success', 'Thanh toán thành công!');
        } else {
            $order->update([
                'payment_status' => 'failed',
            ]);

            return redirect()->route('payment.method', $order)->with('error', 'Thanh toán thất bại. Vui lòng thử lại');
        }
    }

    public function success($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);

        return view('order-success', compact('order'));
    }
}
