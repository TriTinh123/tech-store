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
                return response()->json(['error' => 'Product not found'], 404);
            }

            return redirect()->back()->with('error', 'Product not found');
        }

        $quantity = $request->input('quantity', 1);
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
                'message' => 'Added to cart successfully',
                'cart_count' => $cartCount,
                'product_name' => $product->name,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Added to cart successfully');
    }

    public function buyNow($id, Request $request)
    {
        $product = Product::find($id);
        if (! $product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $quantity = $request->input('quantity', 1);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }

        session()->put('cart', $cart);

        return redirect()->route('checkout.show');
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
                'message' => 'Product removed from cart',
                'cart_count' => $cartCount,
                'total' => $total,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product removed from cart');
    }

    public function update($id, Request $request)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if ($quantity <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $quantity;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Cart updated');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Cart cleared');
    }
}
