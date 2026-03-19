<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->get();

        return view('wishlist', compact('items'));
    }

    public function toggle($productId)
    {
        $userId = auth()->id();
        $existing = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Wishlist::create(['user_id' => $userId, 'product_id' => $productId]);
            $liked = true;
        }

        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json(['ok' => true, 'liked' => $liked, 'count' => $count]);
    }
}
