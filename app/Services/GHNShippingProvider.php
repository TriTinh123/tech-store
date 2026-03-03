<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHNShippingProvider extends ShippingProvider
{
    private $token;

    private $shopId;

    public function __construct()
    {
        $this->token = config('services.ghn.token');
        $this->shopId = config('services.ghn.shop_id');
        $this->apiUrl = 'https://api.ghn.vn/v2';
    }

    public function getTrackingInfo($trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Shop-id' => $this->shopId,
            ])->get("{$this->apiUrl}/tracking/share", [
                'order_code' => $trackingNumber,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'status' => $this->mapGHNStatus($data['data']['status']),
                    'tracking_number' => $trackingNumber,
                    'provider' => 'GHN',
                    'timeline' => $data['data']['log'] ?? [],
                    'estimated_delivery' => $data['data']['estimated_deliver_time'] ?? null,
                ];
            }
            throw new \Exception('GHN API error');
        } catch (\Exception $e) {
            throw new \Exception('GHN tracking failed: '.$e->getMessage());
        }
    }

    public function createShipment($order)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Shop-id' => $this->shopId,
            ])->post("{$this->apiUrl}/order/create", [
                'payment_type_id' => 1, // Cash on delivery
                'note' => $order->notes ?? 'Order '.$order->order_number,
                'required_note' => 'KHONGCHOXEMHANG',
                'to_name' => $order->customer_name,
                'to_phone' => $order->customer_phone,
                'to_address' => $order->delivery_address,
                'to_ward_code' => $order->ward_code ?? '',
                'to_district_id' => $order->district_id ?? 0,
                'to_province_id' => $order->province_id ?? 0,
                'cod_amount' => (int) $order->total_amount,
                'content' => 'Goods',
                'weight' => 500,
                'length' => 10,
                'width' => 10,
                'height' => 10,
                'pick_station_id' => config('services.ghn.pick_station_id'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => (int) $item->price,
                    ];
                })->toArray(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'tracking_number' => $data['data']['order_code'],
                    'order_id' => $data['data']['order_id'],
                ];
            }
            throw new \Exception('GHN shipment creation failed');
        } catch (\Exception $e) {
            throw new \Exception('GHN shipment creation error: '.$e->getMessage());
        }
    }

    public function cancelShipment($trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Shop-id' => $this->shopId,
            ])->post("{$this->apiUrl}/order/cancel", [
                'order_codes' => [$trackingNumber],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            throw new \Exception('GHN cancellation failed: '.$e->getMessage());
        }
    }

    public function estimateDelivery($deliveryAddress)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Shop-id' => $this->shopId,
            ])->get("{$this->apiUrl}/shipping-order/estimateFee", [
                'service_type_id' => 2, // Standard service
                'from_district_id' => config('services.ghn.from_district'),
                'to_district_id' => $deliveryAddress['district_id'] ?? 0,
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }
            throw new \Exception('GHN estimation failed');
        } catch (\Exception $e) {
            throw new \Exception('GHN estimation error: '.$e->getMessage());
        }
    }

    private function mapGHNStatus($status)
    {
        $statusMap = [
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking' => 'Đang lấy hàng',
            'cancel' => 'Hủy đơn',
            'on_hand' => 'Chưa lấy',
            'lost' => 'Mất hàng',
            'damage' => 'Hỏng hàng',
            'delivery' => 'Đang giao',
            'delivered' => 'Đã giao',
            'waiting_to_return' => 'Chờ hoàn trả',
            'return' => 'Đang hoàn trả',
            'returned' => 'Đã hoàn trả',
        ];

        return $statusMap[$status] ?? $status;
    }
}
