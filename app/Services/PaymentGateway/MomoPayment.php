<?php

namespace App\Services\PaymentGateway;

class MomoPayment implements PaymentGatewayInterface
{
    protected $partnerCode;

    protected $accessKey;

    protected $secretKey;

    protected $endpoint = 'https://test-payment.momo.vn/v3/gateway/api/create';

    public function __construct()
    {
        $this->partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20130515');
        $this->accessKey = env('MOMO_ACCESS_KEY', '');
        $this->secretKey = env('MOMO_SECRET_KEY', '');
    }

    /**
     * Process Momo payment
     */
    public function process($order)
    {
        $reference = 'MOMO'.$order->id.time();

        $order->update([
            'payment_method' => 'e_wallet',
            'payment_gateway' => 'momo',
            'payment_reference' => $reference,
            'payment_status' => 'pending',
        ]);

        // Generate Momo payment link
        $paymentLink = $this->generatePaymentLink($order, $reference);

        return [
            'success' => true,
            'message' => 'Chuyển hướng tới Momo...',
            'order' => $order,
            'reference' => $reference,
            'payment_url' => $paymentLink,
        ];
    }

    /**
     * Generate Momo payment link
     */
    protected function generatePaymentLink($order, $reference)
    {
        // For demo, generate a simple QR-style link
        // In production, call Momo API to get actual payment link

        $message = urlencode('TechStore - Deposit');
        $amount = intval($order->total_amount);

        // Momo payment URL format
        return "https://payment.momo.vn/web/index.php?action=payUsingToken&partnerId={$this->partnerCode}&accessKey={$this->accessKey}&amount={$amount}&orderId={$reference}&orderLabel={$message}&returnUrl=".route('payment.callback', ['gateway' => 'momo']);
    }

    /**
     * Verify payment status
     */
    public function verify($reference)
    {
        // Would check Momo API for transaction status
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
        $paymentUrl = $this->generatePaymentLink($order, $order->payment_reference ?? 'MOMO'.$order->id.time());

        return [
            'title' => 'Thanh Toán Qua Momo',
            'description' => 'Quét QR code hoặc sử dụng ứng dụng Momo',
            'icon' => 'fas fa-mobile-alt',
            'color' => '#c2185b',
            'amount' => $order->total_amount,
            'payment_url' => $paymentUrl,
            'qr_url' => $paymentUrl,
            'qr_placeholder' => 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f0f0f0" width="200" height="200"/%3E%3Ctext x="100" y="100" text-anchor="middle" dy=".3em" font-size="14"%3EMomo QR Code%3C/text%3E%3C/svg%3E',
            'note' => 'Quét mã QR bằng ứng dụng Momo để thanh toán. Đơn hàng sẽ tự động xác nhận khi thanh toán thành công.',
            'phone' => '0988888888',
            'steps' => [
                'Mở ứng dụng Momo',
                'Quét QR code hoặc nhập thông tin',
                'Xác nhận thanh toán',
                'Đơn hàng sẽ được xác nhận ngay',
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
