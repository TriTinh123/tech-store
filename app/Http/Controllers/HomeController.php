<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by category slug
        if ($request->has('category') && $request->category != 'all') {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search by name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        $categories = Category::all();
        $featured_products = Product::where('is_featured', true)->limit(6)->get();

        // Flash sale: rotate 5 different products each day using today's date as seed
        $allIds = Product::pluck('id')->toArray();
        $daySeed = (int) date('Ymd'); // e.g. 20260317
        mt_srand($daySeed);
        shuffle($allIds);
        $flashSaleIds = array_slice($allIds, 0, 10);
        $flashSaleProducts = Product::whereIn('id', $flashSaleIds)->get()->sortBy(function($p) use ($flashSaleIds) {
            return array_search($p->id, $flashSaleIds);
        })->values();

        // Generate a daily discount % per flash sale product (10–35%), seeded by day+product id
        $flashSaleDiscounts = [];
        foreach ($flashSaleProducts as $product) {
            mt_srand($daySeed + $product->id);
            $flashSaleDiscounts[$product->id] = mt_rand(10, 35);
        }

        return view('home', [
            'products' => $products,
            'categories' => $categories,
            'featured_products' => $featured_products,
            'current_sort' => $sort,
            'flashSaleProducts' => $flashSaleProducts,
            'flashSaleDiscounts' => $flashSaleDiscounts,
        ]);
    }

    public function getProductsByCategory($category)
    {
        $cat = Category::where('slug', $category)->first();
        if (! $cat) {
            return response()->json([]);
        }
        $products = Product::where('category_id', $cat->id)->get();

        return response()->json($products);
    }
}
