<?php

namespace App\Services\PaymentGateway;

class PaymentService
{
    protected $gateways = [];

    public function __construct()
    {
        $this->gateways = [
            'cod' => new CODPayment,
            'bank_transfer' => new BankTransferPayment,
            'momo' => new MomoPayment,
            'zalopay' => new ZaloPayPayment,
        ];
    }

    /**
     * Get payment gateway instance
     */
    public function getGateway($gateway)
    {
        return $this->gateways[$gateway] ?? null;
    }

    /**
     * Get all available gateways
     */
    public function getAvailableGateways()
    {
        return [
            'cod' => [
                'label' => 'Cash on Delivery',
                'gateway' => 'cod',
                'icon' => 'fas fa-hand-holding-usd',
            ],
            'bank_transfer' => [
                'label' => 'Bank Transfer',
                'gateway' => 'bank_transfer',
                'icon' => 'fas fa-university',
            ],
            'momo' => [
                'label' => 'Pay via Momo',
                'gateway' => 'momo',
                'icon' => 'fas fa-mobile-alt',
            ],
            'zalopay' => [
                'label' => 'Pay via Zalo Pay',
                'gateway' => 'zalopay',
                'icon' => 'fas fa-qrcode',
            ],
        ];
    }

    /**
     * Process payment
     */
    public function processPayment($gateway, $order)
    {
        $paymentGateway = $this->getGateway($gateway);

        if (! $paymentGateway) {
            return [
                'success' => false,
                'message' => 'Invalid payment method',
            ];
        }

        return $paymentGateway->process($order);
    }

    /**
     * Verify payment
     */
    public function verifyPayment($gateway, $reference)
    {
        $paymentGateway = $this->getGateway($gateway);

        if (! $paymentGateway) {
            return [
                'success' => false,
                'message' => 'Invalid payment method',
            ];
        }

        return $paymentGateway->verify($reference);
    }
}
