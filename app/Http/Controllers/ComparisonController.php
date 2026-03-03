<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Models\Product;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    /**
     * Show comparison page
     */
    public function index()
    {
        $products = [];
        $compareList = [];

        if (auth()->check()) {
            // Get from database for authenticated users
            $comparisons = auth()->user()->comparisons()->with('product')->get();
            $products = $comparisons->pluck('product');
            $compareList = $comparisons->pluck('product_id')->toArray();
        } else {
            // Get from session for guests
            $compareList = session()->get('compare', []);
            if (! empty($compareList)) {
                $products = Product::whereIn('id', $compareList)->get();
            }
        }

        return view('comparison.index', compact('products', 'compareList'));
    }

    /**
     * Add product to comparison
     */
    public function add($productId, Request $request)
    {
        $product = Product::findOrFail($productId);

        if (auth()->check()) {
            // Database for authenticated users
            $count = auth()->user()->comparisons()->count();

            if ($count >= 3 && ! auth()->user()->comparisons()->where('product_id', $productId)->exists()) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Chỉ có thể so sánh tối đa 3 sản phẩm'], 400);
                }

                return redirect()->back()->with('error', 'Chỉ có thể so sánh tối đa 3 sản phẩm');
            }

            Comparison::firstOrCreate([
                'user_id' => auth()->id(),
                'product_id' => $productId,
            ]);
        } else {
            // Session for guests
            $compareList = session()->get('compare', []);

            if (count($compareList) >= 3) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Chỉ có thể so sánh tối đa 3 sản phẩm'], 400);
                }

                return redirect()->back()->with('error', 'Chỉ có thể so sánh tối đa 3 sản phẩm');
            }

            if (! in_array($productId, $compareList)) {
                $compareList[] = $productId;
                session()->put('compare', $compareList);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã thêm vào so sánh']);
        }

        return redirect()->back()->with('success', 'Đã thêm vào so sánh');
    }

    /**
     * Remove product from comparison
     */
    public function remove($productId, Request $request)
    {
        if (auth()->check()) {
            // Database for authenticated users
            Comparison::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->delete();
        } else {
            // Session for guests
            $compareList = session()->get('compare', []);
            $compareList = array_diff($compareList, [$productId]);
            session()->put('compare', $compareList);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa khỏi so sánh']);
        }

        return redirect()->back()->with('success', 'Đã xóa khỏi so sánh');
    }

    /**
     * Clear all comparisons
     */
    public function clear()
    {
        if (auth()->check()) {
            // Database for authenticated users
            auth()->user()->comparisons()->delete();
        } else {
            // Session for guests
            session()->forget('compare');
        }

        return redirect()->route('comparison.index')->with('success', 'Đã xóa danh sách so sánh');
    }
}
