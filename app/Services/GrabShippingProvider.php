<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GrabShippingProvider extends ShippingProvider
{
    private $accessToken;

    private $partnerId;

    public function __construct()
    {
        $this->accessToken = config('services.grab.access_token');
        $this->partnerId = config('services.grab.partner_id');
        $this->apiUrl = 'https://api.grab.com/grabexpress/v1';
    }

    public function getTrackingInfo($trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
            ])->get("{$this->apiUrl}/delivery/{$trackingNumber}");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'status' => $this->mapGrabStatus($data['status']),
                    'tracking_number' => $trackingNumber,
                    'provider' => 'Grab',
                    'driver_info' => $data['driver_details'] ?? null,
                    'estimated_delivery' => $data['estimated_delivery_time'] ?? null,
                    'last_location' => $data['last_location'] ?? null,
                ];
            }
            throw new \Exception('Grab API error');
        } catch (\Exception $e) {
            throw new \Exception('Grab tracking failed: '.$e->getMessage());
        }
    }

    public function createShipment($order)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
            ])->post("{$this->apiUrl}/deliveries", [
                'partner_id' => $this->partnerId,
                'delivery_method' => 'SEND_BY_MERCHANT',
                'shipment' => [
                    'origin_address' => [
                        'address' => config('services.grab.pickup_address'),
                        'lat' => config('services.grab.pickup_lat'),
                        'lng' => config('services.grab.pickup_lng'),
                    ],
                    'destination_address' => [
                        'address' => $order->delivery_address,
                        'lat' => $order->latitude ?? 0,
                        'lng' => $order->longitude ?? 0,
                    ],
                ],
                'recipient' => [
                    'name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'email' => $order->customer_email,
                ],
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'value' => (int) $item->price,
                    ];
                })->toArray(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'tracking_number' => $data['delivery_id'],
                    'pickup_code' => $data['pickup_code'] ?? null,
                ];
            }
            throw new \Exception('Grab shipment creation failed');
        } catch (\Exception $e) {
            throw new \Exception('Grab shipment error: '.$e->getMessage());
        }
    }

    public function cancelShipment($trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
            ])->post("{$this->apiUrl}/delivery/{$trackingNumber}/cancel");

            return $response->successful();
        } catch (\Exception $e) {
            throw new \Exception('Grab cancellation failed: '.$e->getMessage());
        }
    }

    public function estimateDelivery($deliveryAddress)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
            ])->post("{$this->apiUrl}/quote", [
                'origin_address' => [
                    'address' => config('services.grab.pickup_address'),
                    'lat' => config('services.grab.pickup_lat'),
                    'lng' => config('services.grab.pickup_lng'),
                ],
                'destination_address' => [
                    'address' => $deliveryAddress['address'] ?? '',
                    'lat' => $deliveryAddress['lat'] ?? 0,
                    'lng' => $deliveryAddress['lng'] ?? 0,
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            throw new \Exception('Grab estimation failed');
        } catch (\Exception $e) {
            throw new \Exception('Grab estimation error: '.$e->getMessage());
        }
    }

    private function mapGrabStatus($status)
    {
        $statusMap = [
            'PENDING_PICKUP' => 'Chờ lấy hàng',
            'PICKING_UP' => 'Đang lấy hàng',
            'PICKED_UP' => 'Đã lấy hàng',
            'CONFIRMED_PICKUP' => 'Xác nhận lấy hàng',
            'PICKING_UP_FAILED' => 'Lấy hàng thất bại',
            'PENDING_DROPOFF' => 'Chờ giao hàng',
            'DROPPING_OFF' => 'Đang giao hàng',
            'DROPPED_OFF' => 'Đã giao hàng',
            'CONFIRMED_DROPOFF' => 'Xác nhận giao hàng',
            'DROPOFF_FAILED' => 'Giao hàng thất bại',
            'CANCELLED' => 'Hủy đơn',
            'RETURNED' => 'Đã hoàn trả',
        ];

        return $statusMap[$status] ?? $status;
    }
}
