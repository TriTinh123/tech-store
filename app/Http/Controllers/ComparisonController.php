<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Models\Product;

class ComparisonController extends Controller
{
    public function index()
    {
        $productIds = Comparison::where('user_id', auth()->id())->pluck('product_id');
        $products   = Product::with('reviews')->whereIn('id', $productIds)->get();
        return view('compare', compact('products'));
    }

    public function toggle($productId)
    {
        $userId   = auth()->id();
        $existing = Comparison::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $inCompare = false;
        } else {
            $count = Comparison::where('user_id', $userId)->count();
            if ($count >= 3) {
                return response()->json(['ok' => false, 'message' => 'You can only compare up to 3 products!']);
            }
            Comparison::create(['user_id' => $userId, 'product_id' => $productId]);
            $inCompare = true;
        }

        $count = Comparison::where('user_id', $userId)->count();
        return response()->json(['ok' => true, 'in_compare' => $inCompare, 'count' => $count]);
    }

    public function clear()
    {
        Comparison::where('user_id', auth()->id())->delete();
        return response()->json(['ok' => true]);
    }

    public function count()
    {
        $count = Comparison::where('user_id', auth()->id())->count();
        return response()->json(['count' => $count]);
    }
}
