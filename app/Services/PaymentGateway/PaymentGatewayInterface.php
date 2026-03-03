<?php

namespace App\Services\PaymentGateway;

interface PaymentGatewayInterface
{
    /**
     * Process payment
     */
    public function process($order);

    /**
     * Verify payment status
     */
    public function verify($reference);

    /**
     * Get payment details for display
     */
    public function getPaymentDetails($order);

    /**
     * Check if payment method requires redirect
     */
    public function requiresRedirect();
}
