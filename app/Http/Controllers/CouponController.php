<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate and apply coupon
     */
    public function apply(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string|max:50',
            'cart_total' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();

        if (! $coupon) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại'], 404);
        }

        if (! $coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không còn hợp lệ'], 400);
        }

        if ($validated['cart_total'] < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng tối thiểu là '.number_format($coupon->min_order_amount, 0, ',', '.').'₫',
            ], 400);
        }

        $discount = $coupon->calculateDiscount($validated['cart_total']);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'coupon' => $coupon,
            'discount_amount' => $discount,
        ]);
    }
}
