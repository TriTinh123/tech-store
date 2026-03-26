@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h1 style="font-size: 32px; color: #333; margin-bottom: 10px;">
            <i class="fas fa-shopping-bags"></i> All Products
        </h1>
        <p style="color: #666; font-size: 14px;">Search high-quality tech accessories</p>
    </div>

    <div style="display: grid; grid-template-columns: 220px 1fr; gap: 20px;">
        <!-- Sidebar Filters -->
        <div>
            <!-- Categories -->
            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
                <div style="background: #00b894; color: white; padding: 15px; font-weight: 600;">
                    <i class="fas fa-filter"></i> CATEGORIES
                </div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="border-bottom: 1px solid #f0f0f0;">
                        <a href="{{ route('products.index') }}" style="display: block; padding: 12px 15px; text-decoration: none; color: #333; transition: all 0.3s;">
                            ✓ All
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li style="border-bottom: 1px solid #f0f0f0;">
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                               style="display: block; padding: 12px 15px; text-decoration: none; color: #333; transition: all 0.3s; @if(request('category') == $category->slug) background: #e8f8f5; color: #00b894; font-weight: 600; @endif"
                               onmouseover="this.style.background='#f9f9f9'; this.style.color='#00b894';"
                               onmouseout="@if(request('category') != $category->slug) this.style.background=''; this.style.color='#333'; @endif">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Sort Options -->
            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="background: #00b894; color: white; padding: 15px; font-weight: 600;">
                    <i class="fas fa-sort"></i> SORT
                </div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="border-bottom: 1px solid #f0f0f0;">
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'latest'])) }}" 
                           style="display: block; padding: 12px 15px; text-decoration: none; color: #333; @if(request('sort', 'latest') == 'latest') background: #e8f8f5; color: #00b894; font-weight: 600; @endif">
                            Newest
                        </a>
                    </li>
                    <li style="border-bottom: 1px solid #f0f0f0;">
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price-low'])) }}" 
                           style="display: block; padding: 12px 15px; text-decoration: none; color: #333; @if(request('sort') == 'price-low') background: #e8f8f5; color: #00b894; font-weight: 600; @endif">
                            Price: Low to High
                        </a>
                    </li>
                    <li style="border-bottom: 1px solid #f0f0f0;">
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price-high'])) }}" 
                           style="display: block; padding: 12px 15px; text-decoration: none; color: #333; @if(request('sort') == 'price-high') background: #e8f8f5; color: #00b894; font-weight: 600; @endif">
                            Price: High to Low
                        </a>
                    </li>
                    <li style="border-bottom: 1px solid #f0f0f0;">
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'bestseller'])) }}" 
                           style="display: block; padding: 12px 15px; text-decoration: none; color: #333; @if(request('sort') == 'bestseller') background: #e8f8f5; color: #00b894; font-weight: 600; @endif">
                            Best Seller
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'rating'])) }}" 
                           style="display: block; padding: 12px 15px; text-decoration: none; color: #333; @if(request('sort') == 'rating') background: #e8f8f5; color: #00b894; font-weight: 600; @endif">
                            Top Rated
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Products Grid -->
        <div>
            <!-- Product Count -->
            <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <p style="color: #666; font-size: 14px;">
                    Show <strong>{{ $products->count() }}</strong> / <strong>{{ $products->total() }}</strong> products
                </p>
            </div>

            <!-- Products -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
                @forelse($products as $product)
                    <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid #f0f0f0;"
                         onmouseover="this.style.boxShadow='0 8px 20px rgba(0,0,0,0.12)'; this.style.transform='translateY(-8px)';"
                         onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'; this.style.transform='none';">
                        
                        <!-- Product Image -->
                        <div style="position: relative; width: 100%; height: 200px; background: #f5f5f5; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            @if($product->discount_percentage > 0)
                                <span style="position: absolute; top: 10px; right: 10px; background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: 600;">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            @endif
                            <img src="{{ $product->image ?? asset('images/no-image.svg') }}" 
                                 alt="{{ $product->name }}" 
                                 style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>

                        <!-- Product Info -->
                        <div style="padding: 15px;">
                            <h3 style="font-size: 13px; margin-bottom: 8px; line-height: 1.4; min-height: 32px;">
                                <a href="{{ route('product.show', $product->id) }}" style="text-decoration: none; color: #333;">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            @if($product->rating)
                                <div style="font-size: 12px; color: #ffa500; margin-bottom: 8px;">
                                    @for($i = 0; $i < 5; $i++)
                                        @if($i < $product->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    ({{ $product->reviews_count ?? 0 }})
                                </div>
                            @endif

                            <!-- Price -->
                            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                                <span style="font-size: 18px; font-weight: 900; color: #ff6b6b;">
                                    ${{ number_format($product->price, 2) }}
                                </span>
                                @if($product->original_price)
                                    <span style="font-size: 12px; text-decoration: line-through; color: #ccc;">
                                        ${{ number_format($product->original_price, 2) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('product.show', $product->id) }}" 
                                   style="flex: 1; display: flex; align-items: center; justify-content: center; background: #00b894; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; padding: 8px; transition: background 0.3s;"
                                   onmouseover="this.style.background='#00a080';"
                                   onmouseout="this.style.background='#00b894';">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" style="flex: 1;" class="add-to-cart-form">
                                    @csrf
                                    <button type="submit" 
                                            style="width: 100%; padding: 8px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600; transition: background 0.3s;"
                                            onmouseover="this.style.background='#ff5252';"
                                            onmouseout="this.style.background='#ff6b6b';">
                                        <i class="fas fa-shopping-cart"></i> Cart
                                    </button>
                                </form>
                            </div>
                            @auth
                            <form action="{{ route('cart.buy-now', $product->id) }}" method="POST" style="margin-top: 8px;">
                                @csrf
                                <button type="submit"
                                        style="width: 100%; padding: 8px; background: linear-gradient(135deg,#f97316,#ea580c); color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 700; transition: opacity 0.2s;"
                                        onmouseover="this.style.opacity='.85';"
                                        onmouseout="this.style.opacity='1';">
                                    <i class="fas fa-bolt"></i> Buy Now
                                </button>
                            </form>
                            @else
                            <a href="{{ route('login') }}"
                               style="display:block;margin-top:8px;width:100%;padding:8px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border:none;border-radius:3px;font-size:12px;font-weight:700;text-align:center;text-decoration:none;">
                                <i class="fas fa-bolt"></i> Buy Now
                            </a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                        <i class="fas fa-box" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                        <p style="color: #999;">No products found</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div style="display: flex; justify-content: center; gap: 5px;">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    a[rel="next"], a[rel="prev"] {
        display: inline-block;
        padding: 8px 12px;
        background: #00b894;
        color: white;
        text-decoration: none;
        border-radius: 3px;
        font-size: 12px;
        transition: background 0.3s;
    }
    a[rel="next"]:hover, a[rel="prev"]:hover {
        background: #00a080;
    }
    .pagination span, .pagination a {
        display: inline-block;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 3px;
        font-size: 12px;
    }
    .pagination a {
        background: #f5f5f5;
        color: #333;
        cursor: pointer;
        transition: all 0.3s;
    }
    .pagination a:hover {
        background: #00b894;
        color: white;
    }
    .pagination span.current {
        background: #00b894;
        color: white;
        padding: 8px 12px;
    }
</style>
@endsection
