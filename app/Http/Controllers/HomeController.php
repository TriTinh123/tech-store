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
        $sort = $request->get('sort', 'newest');
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

        return view('home', [
            'products' => $products,
            'categories' => $categories,
            'featured_products' => $featured_products,
            'current_sort' => $sort,
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
