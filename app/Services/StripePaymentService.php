<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentService extends PaymentService
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function initiate()
    {
        $items = $this->order->items()->get();

        $lineItems = $items->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'vnd',
                    'product_data' => [
                        'name' => $item->product_name,
                        'description' => 'Order: '.$this->order->order_number,
                    ],
                    'unit_amount' => intval($item->price * 100), // Stripe uses cents
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('payment.success', $this->order),
                'cancel_url' => route('payment.cancel', $this->order),
                'metadata' => [
                    'order_id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                ],
            ]);

            $this->order->update([
                'payment_reference' => $session->id,
                'payment_gateway' => 'stripe',
            ]);

            return $session;
        } catch (\Exception $e) {
            throw new \Exception('Stripe payment initiation failed: '.$e->getMessage());
        }
    }

    public function verify($sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $this->order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'paid_at' => now(),
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            throw new \Exception('Stripe verification failed: '.$e->getMessage());
        }
    }

    public function getPaymentDetails()
    {
        return [
            'gateway' => 'stripe',
            'reference' => $this->order->payment_reference,
            'status' => $this->order->payment_status,
            'amount' => $this->order->total_amount,
        ];
    }
}
