<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /** AJAX: apply coupon code, returns discount info */
    public function apply(Request $request)
    {
        $request->validate(['code' => 'required|string', 'total' => 'required|numeric|min:0']);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (!$coupon) {
            return response()->json(['ok' => false, 'message' => 'Coupon not found.']);
        }

        $total = (float) $request->total;

        if (!$coupon->isValid($total)) {
            if (!$coupon->is_active || ($coupon->expires_at && $coupon->expires_at->isPast())) {
                return response()->json(['ok' => false, 'message' => 'Coupon has expired or is no longer valid.']);
            }
            if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                return response()->json(['ok' => false, 'message' => 'Coupon has reached its usage limit.']);
            }
            return response()->json(['ok' => false, 'message' => 'Minimum order of ' . number_format($coupon->min_order_amount, 0, ',', '.') . '₫ required to use this coupon.']);
        }

        $discount = $coupon->calcDiscount($total);

        return response()->json([
            'ok'       => true,
            'discount' => $discount,
            'final'    => $total - $discount,
            'message'  => 'Coupon applied! Discount: ' . number_format($discount, 0, ',', '.') . '₫',
        ]);
    }
}
