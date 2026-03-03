<?php

namespace App\Services\PaymentGateway;

class ZaloPayPayment implements PaymentGatewayInterface
{
    protected $appId;

    protected $key1;

    protected $key2;

    protected $endpoint = 'https://sandbox.zalopay.com.vn/api/v2/create';

    public function __construct()
    {
        $this->appId = env('ZALOPAY_APP_ID', '2553');
        $this->key1 = env('ZALOPAY_KEY1', '');
        $this->key2 = env('ZALOPAY_KEY2', '');
    }

    /**
     * Process Zalo Pay payment
     */
    public function process($order)
    {
        $reference = 'ZLP'.$order->id.time();

        $order->update([
            'payment_method' => 'e_wallet',
            'payment_gateway' => 'zalopay',
            'payment_reference' => $reference,
            'payment_status' => 'pending',
        ]);

        // Generate Zalo Pay payment link
        $paymentLink = $this->generatePaymentLink($order, $reference);

        return [
            'success' => true,
            'message' => 'Chuyển hướng tới Zalo Pay...',
            'order' => $order,
            'reference' => $reference,
            'payment_url' => $paymentLink,
        ];
    }

    /**
     * Generate Zalo Pay link
     */
    protected function generatePaymentLink($order, $reference)
    {
        $amount = intval($order->total_amount * 100); // Zalo Pay uses cents
        $description = 'TechStore - Order '.$order->order_number;

        // Zalo Pay payment URL format (simplified for demo)
        return "https://sandbox.zalopay.com.vn/web/index.php?action=payUsingToken&appId={$this->appId}&amount={$amount}&appTransId={$reference}&description=".urlencode($description).'&returnUrl='.route('payment.callback', ['gateway' => 'zalopay']);
    }

    /**
     * Verify payment status
     */
    public function verify($reference)
    {
        // Would check Zalo Pay API for transaction status
        return [
            'success' => false,
            'message' => 'Đang kiểm tra trạng thái thanh toán...',
        ];
    }

    /**
     * Get payment details for display
     */
    public function getPaymentDetails($order)
    {
        $paymentUrl = $this->generatePaymentLink($order, $order->payment_reference ?? 'ZLP'.$order->id.time());

        return [
            'title' => 'Thanh Toán Qua Zalo Pay',
            'description' => 'Thanh toán nhanh chóng qua ví Zalo',
            'icon' => 'fas fa-qrcode',
            'color' => '#0085ff',
            'amount' => $order->total_amount,
            'payment_url' => $paymentUrl,
            'qr_url' => $paymentUrl,
            'qr_placeholder' => 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f0f0f0" width="200" height="200"/%3E%3Ctext x="100" y="100" text-anchor="middle" dy=".3em" font-size="14"%3EZalo Pay QR Code%3C/text%3E%3C/svg%3E',
            'note' => 'Quét mã QR bằng ứng dụng Zalo để thanh toán. Đơn hàng sẽ tự động xác nhận khi thanh toán thành công.',
            'phone' => '0977777777',
            'steps' => [
                'Mở ứng dụng Zalo',
                'Chọn Zalo Pay',
                'Quét QR hoặc nhập thông tin',
                'Xác nhận và hoàn tất',
            ],
        ];
    }

    /**
     * Check if payment method requires redirect
     */
    public function requiresRedirect()
    {
        return true;
    }
}
