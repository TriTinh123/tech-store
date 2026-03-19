<?php

namespace App\Services\PaymentGateway;

class MomoPayment implements PaymentGatewayInterface
{
    protected $partnerCode;

    protected $accessKey;

    protected $secretKey;

    // Official MoMo test endpoint
    protected $endpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';

    public function __construct()
    {
        // Default = public sandbox test credentials from MoMo docs
        $this->partnerCode = env('MOMO_PARTNER_CODE', 'MOMO');
        $this->accessKey   = env('MOMO_ACCESS_KEY',   'F8BBA842ECF85');
        $this->secretKey   = env('MOMO_SECRET_KEY',   'K951B6PE1waDMi640xX08PD3vg6EkVlz');
    }

    /**
     * Process Momo payment
     */
    public function process($order)
    {
        $reference = 'MOMO' . $order->id . date('mdHi');

        $order->update([
            'payment_method'    => 'e_wallet',
            'payment_gateway'   => 'momo',
            'payment_reference' => $reference,
            'payment_status'    => 'pending',
        ]);

        return [
            'success'   => true,
            'message'   => 'Please scan the MoMo QR code to complete payment.',
            'order'     => $order,
            'reference' => $reference,
        ];
    }

    /**
     * Generate MoMo payment link by calling the real test-payment API.
     * Signs the request with HMAC-SHA256 using secretKey.
     */
    protected function generatePaymentLink($order, $reference)
    {
        $requestId   = $reference;
        $orderId     = $reference;
        $amount      = (int) $order->total_amount;
        $orderInfo   = 'TechStore - ' . $order->order_number;
        $requestType = 'payWithMethod';
        $redirectUrl = route('checkout.payment.callback', ['gateway' => 'momo']);
        $ipnUrl      = route('checkout.payment.callback', ['gateway' => 'momo']);
        $extraData   = '';
        $lang        = 'vi';

        // Signature raw string — keys must be in alphabetical order
        $rawSignature = "accessKey={$this->accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$ipnUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$this->partnerCode}"
            . "&redirectUrl={$redirectUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";
        $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

        $body = json_encode([
            'partnerCode' => $this->partnerCode,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'requestType' => $requestType,
            'extraData'   => $extraData,
            'lang'        => $lang,
            'signature'   => $signature,
        ]);

        try {
            $ch = curl_init($this->endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $raw    = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            unset($ch);

            if ($status === 200 && $raw) {
                $res = json_decode($raw, true);
                if (!empty($res['payUrl'])) {
                    return $res['payUrl'];  // real MoMo payment page
                }
                \Log::warning('MoMo API error: ' . $raw);
            }
        } catch (\Throwable $e) {
            \Log::warning('MoMo exception: ' . $e->getMessage());
        }

        // Fallback: show local confirm page for demo
        return route('checkout.payment.confirm', ['order' => $order->id, 'gateway' => 'momo']);
    }

    /**
     * Verify payment status
     */
    public function verify($reference)
    {
        // Would check Momo API for transaction status
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
            'title'   => 'MoMo Wallet',
            'amount'  => $order->total_amount,
            'qr_path' => '/images/qr/momo-qr.png',
            'note'    => 'Scan the QR code with the MoMo app. Please transfer the exact amount and include the order number in the message.',
            'ref'     => $order->order_number ?? ('ORD-' . $order->id),
            'steps'   => [
                'Open the MoMo app on your phone',
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
