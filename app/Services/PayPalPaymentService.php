<?php

namespace App\Services;

use App\Models\Order;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PayPalPaymentService extends PaymentService
{
    private $client;

    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $environment = new SandboxEnvironment(
            config('services.paypal.client_id'),
            config('services.paypal.secret')
        );
        $this->client = new PayPalHttpClient($environment);
    }

    public function initiate()
    {
        $items = $this->order->items()->get();

        $itemsData = $items->map(function ($item) {
            return [
                'name' => $item->product_name,
                'quantity' => (string) $item->quantity,
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($item->price / 23500, 2, '.', ''), // Convert VND to USD
                ],
            ];
        })->toArray();

        try {
            $request = new OrdersCreateRequest;
            $request->prefer('return=representation');
            $request->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $this->order->order_number,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($this->order->total_amount / 23500, 2, '.', ''),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => 'USD',
                                    'value' => number_format($this->order->total_amount / 23500, 2, '.', ''),
                                ],
                            ],
                        ],
                        'items' => $itemsData,
                    ],
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'landing_page' => 'BILLING',
                    'return_url' => route('payment.success', $this->order),
                    'cancel_url' => route('payment.cancel', $this->order),
                ],
            ];

            $response = $this->client->execute($request);

            if ($response->statusCode === 201) {
                $paypalOrderId = $response->result->id;

                $this->order->update([
                    'payment_reference' => $paypalOrderId,
                    'payment_gateway' => 'paypal',
                ]);

                return $response->result;
            }
            throw new \Exception('PayPal order creation failed');
        } catch (\Exception $e) {
            throw new \Exception('PayPal payment initiation failed: '.$e->getMessage());
        }
    }

    public function verify($paypalOrderId)
    {
        try {
            // In production, verify with PayPal API
            // For now, assume verified if order exists
            $this->order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('PayPal verification failed: '.$e->getMessage());
        }
    }

    public function getPaymentDetails()
    {
        return [
            'gateway' => 'paypal',
            'reference' => $this->order->payment_reference,
            'status' => $this->order->payment_status,
            'amount' => $this->order->total_amount,
        ];
    }
}
