<?php

namespace App\Services\PaymentGateway;

class BankTransferPayment implements PaymentGatewayInterface
{
    protected $bankAccounts = [
        [
            'bank' => 'Vietcombank',
            'account' => '1234567890',
            'holder' => 'TECHSTORE CO., LTD',
            'branch' => 'CN TP. HCM',
        ],
        [
            'bank' => 'ACB (Asia Commercial Bank)',
            'account' => '0987654321',
            'holder' => 'TECHSTORE COMPANY',
            'branch' => 'CN Hà Nội',
        ],
        [
            'bank' => 'MB Bank',
            'account' => '1122334455',
            'holder' => 'TECHSTORE LLC',
            'branch' => 'Chi nhánh Thái Nguyên',
        ],
    ];

    /**
     * Process bank transfer payment
     */
    public function process($order)
    {
        $reference = 'TRF'.$order->id.date('YmdHis');

        $order->update([
            'payment_method' => 'bank_transfer',
            'payment_gateway' => 'bank',
            'payment_reference' => $reference,
            'payment_status' => 'pending',
        ]);

        return [
            'success' => true,
            'message' => 'Vui lòng chuyển khoản theo hướng dẫn',
            'order' => $order,
            'reference' => $reference,
        ];
    }

    /**
     * Verify payment status
     */
    public function verify($reference)
    {
        // In real scenario, this would check the bank API or admin confirmation
        return [
            'success' => false,
            'message' => 'Cần xác nhận từ quản trị viên',
        ];
    }

    /**
     * Get payment details for display
     */
    public function getPaymentDetails($order)
    {
        $reference = 'TRF'.$order->id.date('YmdHis');

        // Generate QR code data for bank transfer
        $qrData = 'https://vietqr.io/970422/1234567890/'.intval($order->total_amount).'/'.urlencode($reference);

        return [
            'title' => 'Thanh Toán Qua Ngân Hàng',
            'description' => 'Chuyển khoản trực tiếp từ ngân hàng của bạn',
            'icon' => 'fas fa-university',
            'color' => '#3498db',
            'amount' => $order->total_amount,
            'reference' => $reference,
            'banks' => $this->bankAccounts,
            'qr_url' => $qrData,
            'note' => 'Vui lòng ghi mã tham chiếu trong nội dung chuyển khoản để chúng tôi xác nhận nhanh chóng. Bạn có thể quét mã QR bằng ứng dụng ngân hàng hoặc ví điện tử.',
            'steps' => [
                'Chọn ngân hàng phù hợp',
                'Chuyển khoản số tiền chỉ định',
                'Ghi mã tham chiếu vào nội dung',
                'Gửi chứng chỉ chuyển khoản cho hỗ trợ',
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
