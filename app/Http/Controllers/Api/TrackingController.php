<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShippingTracker;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track(Order $order)
    {
        if (! auth()->check() || ($order->user_id !== auth()->id() && ! auth()->user()->isAdmin())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (! $order->tracking_number || ! $order->shipping_provider) {
            return response()->json([
                'status' => 'pending',
                'message' => 'Chưa có thông tin vận chuyển',
            ]);
        }

        try {
            $trackingInfo = ShippingTracker::track(
                $order->tracking_number,
                $order->shipping_provider
            );

            return response()->json([
                'success' => true,
                'data' => $trackingInfo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không thể lấy thông tin vận chuyển',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function estimate(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'provider' => 'required|in:GHN,GRAB',
            'district_id' => 'required|integer',
        ]);

        try {
            $estimate = ShippingTracker::estimate(
                $request->all(),
                $request->provider
            );

            return response()->json([
                'success' => true,
                'data' => $estimate,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không thể tính phí vận chuyển',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
