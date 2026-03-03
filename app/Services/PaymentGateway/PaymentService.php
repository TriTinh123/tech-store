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
                'label' => 'Thanh Toán Khi Nhận Hàng',
                'gateway' => 'cod',
                'icon' => 'fas fa-hand-holding-usd',
            ],
            'bank_transfer' => [
                'label' => 'Chuyển Khoản Ngân Hàng',
                'gateway' => 'bank_transfer',
                'icon' => 'fas fa-university',
            ],
            'momo' => [
                'label' => 'Thanh Toán Qua Momo',
                'gateway' => 'momo',
                'icon' => 'fas fa-mobile-alt',
            ],
            'zalopay' => [
                'label' => 'Thanh Toán Qua Zalo Pay',
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
                'message' => 'Phương thức thanh toán không hợp lệ',
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
                'message' => 'Phương thức thanh toán không hợp lệ',
            ];
        }

        return $paymentGateway->verify($reference);
    }
}
