<?php

namespace App\Services\PaymentGateway;

class CODPayment implements PaymentGatewayInterface
{
    /**
     * Process COD payment (Cash On Delivery)
     */
    public function process($order)
    {
        $order->update([
            'payment_method' => 'cod',
            'payment_gateway' => 'cod',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        return [
            'success' => true,
            'message' => 'Đơn hàng sẽ được giao. Vui lòng thanh toán khi nhận hàng',
            'order' => $order,
        ];
    }

    /**
     * Verify payment status
     */
    public function verify($reference)
    {
        // COD doesn't need verification, mark as completed when delivered
        return [
            'success' => false,
            'message' => 'COD không cần xác nhận trực tuyến',
        ];
    }

    /**
     * Get payment details for display
     */
    public function getPaymentDetails($order)
    {
        return [
            'title' => 'Thanh Toán Khi Nhận Hàng (COD)',
            'description' => 'Bạn sẽ thanh toán '.number_format($order->total_amount, 0, ',', '.').'₫ khi nhân viên giao hàng tới.',
            'icon' => 'fas fa-hand-holding-usd',
            'color' => '#00a699',
            'note' => 'Phí giao hàng có thể được tính thêm tùy khu vực',
            'steps' => [
                'Xác nhận đơn hàng',
                'Chờ giao hàng',
                'Thanh toán khi nhận',
            ],
        ];
    }

    /**
     * Check if payment method requires redirect
     */
    public function requiresRedirect()
    {
        return false;
    }
}
