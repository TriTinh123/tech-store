<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Show all products
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$searchTerm.'%');
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Sort options
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::all();
        $searchQuery = $request->get('search', '');

        return view('products.index', compact('products', 'categories', 'searchQuery'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $product->load('reviews');
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->limit(4)
            ->get();

        return view('product-detail', compact('product', 'relatedProducts'));
    }

    /**
     * Store a new review for a product
     */
    public function storeReview(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id() ?? null,
            'user_name' => $validated['user_name'],
            'user_email' => $validated['user_email'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Update product rating and review count
        $reviews = $product->reviews;
        if ($reviews->count() > 0) {
            $product->rating = round($reviews->avg('rating'), 1);
            $product->reviews_count = $reviews->count();
            $product->save();
        }

        return redirect()->route('product.show', $product->id)->with('success', 'Đánh giá của bạn đã được gửi thành công!');
    }
}
