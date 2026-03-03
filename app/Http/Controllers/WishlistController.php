<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's wishlist
     */
    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('product')->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Add product to wishlist
     */
    public function add($productId, Request $request)
    {
        $product = Product::findOrFail($productId);

        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();

        if (! $exists) {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $productId,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Đã thêm vào yêu thích']);
            }

            return redirect()->back()->with('success', 'Đã thêm vào yêu thích');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm đã có trong yêu thích']);
        }

        return redirect()->back()->with('info', 'Sản phẩm đã có trong yêu thích');
    }

    /**
     * Remove product from wishlist
     */
    public function remove($productId, Request $request)
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa khỏi yêu thích']);
        }

        return redirect()->back()->with('success', 'Đã xóa khỏi yêu thích');
    }

    /**
     * Check if product is in wishlist
     */
    public function check($productId)
    {
        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json(['in_wishlist' => $exists]);
    }

    /**
     * Add all wishlist items to cart
     */
    public function addAllToCart()
    {
        $wishlists = auth()->user()->wishlists;
        $cart = session()->get('cart', []);

        foreach ($wishlists as $wishlist) {
            if (! isset($cart[$wishlist->product_id])) {
                $cart[$wishlist->product_id] = 1;
            }
        }

        session()->put('cart', $cart);
        auth()->user()->wishlists()->delete();

        return redirect()->route('cart.index')->with('success', 'Đã thêm tất cả sản phẩm vào giỏ hàng');
    }
}
