<?php

namespace App\Services\PaymentGateway;

class ZaloPayPayment implements PaymentGatewayInterface
{
    protected $appId;

    protected $key1;

    protected $key2;

    // Official ZaloPay sandbox endpoint
    protected $endpoint = 'https://sb-openapi.zalopay.vn/v2/create';

    public function __construct()
    {
        // Default = public sandbox test credentials from ZaloPay docs
        $this->appId = env('ZALOPAY_APP_ID', '2553');
        $this->key1  = env('ZALOPAY_KEY1', 'PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL');
        $this->key2  = env('ZALOPAY_KEY2', 'kLtgPl8HHhfvMuDHPwKfgfsY4Vu/kcrolarsamahplain');
    }

    /**
     * Process Zalo Pay payment
     */
    public function process($order)
    {
        $reference = 'ZLP' . $order->id . date('mdHi');

        $order->update([
            'payment_method'    => 'e_wallet',
            'payment_gateway'   => 'zalopay',
            'payment_reference' => $reference,
            'payment_status'    => 'pending',
        ]);

        return [
            'success'   => true,
            'message'   => 'Please scan the ZaloPay QR code to complete payment.',
            'order'     => $order,
            'reference' => $reference,
        ];
    }

    /**
     * Generate ZaloPay payment link by calling the real sandbox API.
     * Signs the request with HMAC-SHA256 using key1.
     */
    protected function generatePaymentLink($order, $reference)
    {
        $appId       = (int) $this->appId;
        // app_trans_id must be unique per day | format: yymmdd_<unique>
        $appTransId  = date('ymd') . '_' . substr($reference, 0, 20);
        $appUser     = 'user_' . ($order->user_id ?? 'guest');
        $appTime     = (int) (microtime(true) * 1000); // milliseconds
        $amount      = (int) $order->total_amount;     // VND, no multiplier
        $description = 'TechStore - ' . $order->order_number;
        $callbackUrl = route('checkout.payment.callback', ['gateway' => 'zalopay']);
        $embedData   = json_encode(['redirecturl' => $callbackUrl]);
        $item        = '[]';

        // MAC = HMAC-SHA256(key1, "app_id|app_trans_id|app_user|amount|app_time|embed_data|item")
        $rawMac = implode('|', [$appId, $appTransId, $appUser, $amount, $appTime, $embedData, $item]);
        $mac    = hash_hmac('sha256', $rawMac, $this->key1);

        $params = [
            'app_id'       => $appId,
            'app_trans_id' => $appTransId,
            'app_user'     => $appUser,
            'app_time'     => $appTime,
            'amount'       => $amount,
            'item'         => $item,
            'embed_data'   => $embedData,
            'description'  => $description,
            'bank_code'    => '',
            'mac'          => $mac,
        ];

        try {
            $ch = curl_init($this->endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($params),
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $raw    = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($status === 200 && $raw) {
                $res = json_decode($raw, true);
                if (!empty($res['order_url'])) {
                    return $res['order_url'];  // real ZaloPay payment page
                }
                \Log::warning('ZaloPay API error: ' . $raw);
            }
        } catch (\Throwable $e) {
            \Log::warning('ZaloPay exception: ' . $e->getMessage());
        }

        // Fallback: show local confirm page for demo
        return route('checkout.payment.confirm', ['order' => $order->id, 'gateway' => 'zalopay']);
    }

    /**
     * Verify payment status
     */
    public function verify($reference)
    {
        // Would check Zalo Pay API for transaction status
        return [
            'success' => false,
            'message' => 'Checking payment status...',
        ];
    }

    /**
     * Get payment details for display
     */
    public function getPaymentDetails($order)
    {
        return [
            'title'   => 'ZaloPay Wallet',
            'amount'  => $order->total_amount,
            'qr_path' => '/images/qr/zalopay-qr.png',
            'note'    => 'Scan the QR code with the ZaloPay app. Please transfer the exact amount and include the order number in the message.',
            'ref'     => $order->order_number ?? ('ORD-' . $order->id),
            'steps'   => [
                'Open the ZaloPay app on your phone',
                'Tap the QR scan icon',
                'Scan the QR code shown below',
                'Enter the exact amount and order number',
                'Confirm payment',
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
