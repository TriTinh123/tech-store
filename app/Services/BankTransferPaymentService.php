<?php

namespace App\Services;

class BankTransferPaymentService extends PaymentService
{
    public function initiate()
    {
        $bankInfo = config('payment.bank_transfer');

        // Generate QR code URL using QR API (no library needed)
        $qrContent = "Bank Transfer\n".
                    'Account: '.$bankInfo['account_number']."\n".
                    'Amount: '.number_format($this->order->total_amount, 0, ',', '.')." VND\n".
                    'Reference: '.$this->order->order_number;

        // Use free QR code API
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($qrContent);

        $this->order->update([
            'payment_gateway' => 'bank_transfer',
            'payment_status' => 'pending',
            'payment_reference' => json_encode([
                'bank_info' => $bankInfo,
                'amount' => $this->order->total_amount,
                'reference' => $this->order->order_number,
            ]),
        ]);

        return [
            'qr_image' => $qrUrl,
            'bank_info' => $bankInfo,
            'amount' => $this->order->total_amount,
            'order_number' => $this->order->order_number,
        ];
    }

    public function verify($reference)
    {
        // Manual verification by admin or webhook from bank
        // For now, mark as pending
        $this->order->update([
            'payment_status' => 'pending',
            'status' => 'pending_payment',
        ]);

        return true;
    }

    public function getPaymentDetails()
    {
        $reference = json_decode($this->order->payment_reference, true);

        return [
            'gateway' => 'bank_transfer',
            'bank_info' => $reference['bank_info'] ?? null,
            'status' => $this->order->payment_status,
            'amount' => $this->order->total_amount,
        ];
    }
}
