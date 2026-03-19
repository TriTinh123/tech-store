<?php

namespace App\Services\PaymentGateway;

class BankTransferPayment implements PaymentGatewayInterface
{
    public function process($order)
    {
        $reference = 'TS' . $order->id . date('mdHi');

        $order->update([
            'payment_method'    => 'bank_transfer',
            'payment_gateway'   => 'bank',
            'payment_reference' => $reference,
            'payment_status'    => 'pending',
        ]);

        return [
            'success'   => true,
            'message'   => 'Please transfer as per the instructions.',
            'order'     => $order,
            'reference' => $reference,
        ];
    }

    public function verify($reference)
    {
        return ['success' => false, 'message' => 'Requires admin confirmation'];
    }

    public function getPaymentDetails($order)
    {
        $reference = 'TS' . $order->id . date('mdHi');

        return [
            'title'      => 'Bank Transfer',
            'amount'     => $order->total_amount,
            'reference'  => $reference,
            'bank_label' => 'TPBank',
            'note'       => 'Open your banking app, scan the QR code, and complete the transfer.',
            'steps'      => [
                'Open your banking app (TPBank, MoMo, ZaloPay, etc.)',
                'Tap "Scan QR" and scan the QR code below',
                'Enter the transfer note: ' . $reference,
                'Confirm and complete the transfer',
            ],
        ];
    }

    public function requiresRedirect()
    {
        return false;
    }
}
