<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $items = [];

        foreach ($cart as $id => $quantity) {
            $product = Product::find($id);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return view('cart', compact('items', 'total', 'cart'));
    }

    public function add($id, Request $request)
    {
        $product = Product::find($id);
        if (! $product) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
            }

            return redirect()->back()->with('error', 'Sản phẩm không tồn tại');
        }

        $quantity = $request->get('quantity', 1);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }

        session()->put('cart', $cart);

        // Return JSON for AJAX requests
        if ($request->wantsJson()) {
            $cartCount = array_sum($cart);

            return response()->json([
                'success' => true,
                'message' => 'Thêm vào giỏ hàng thành công',
                'cart_count' => $cartCount,
                'product_name' => $product->name,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Thêm vào giỏ hàng thành công');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        // Calculate new total
        $total = 0;
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }

        // Return JSON for AJAX requests
        if (request()->wantsJson()) {
            $cartCount = array_sum($cart);

            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm khỏi giỏ hàng',
                'cart_count' => $cartCount,
                'total' => $total,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Xóa sản phẩm khỏi giỏ hàng');
    }

    public function update($id, Request $request)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->get('quantity', 1);

        if ($quantity <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $quantity;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Đã xóa giỏ hàng');
    }
}
