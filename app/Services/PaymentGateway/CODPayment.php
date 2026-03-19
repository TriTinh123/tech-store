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
            'message' => 'Your order will be delivered. Please pay upon receipt',
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
            'message' => 'COD does not require online confirmation',
        ];
    }

    /**
     * Get payment details for display
     */
    public function getPaymentDetails($order)
    {
        return [
            'title' => 'Cash on Delivery (COD)',
            'description' => 'You will pay '.number_format($order->total_amount, 0, ',', '.').'₫ when the delivery person arrives.',
            'icon' => 'fas fa-hand-holding-usd',
            'color' => '#00a699',
            'note' => 'Shipping fee may vary by area',
            'steps' => [
                'Confirm order',
                'Wait for delivery',
                'Pay upon receipt',
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
