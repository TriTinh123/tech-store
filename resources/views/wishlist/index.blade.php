@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-heart"></i> Danh Sách Yêu Thích
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($wishlists->count() == 0)
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Bạn chưa lưu sản phẩm yêu thích nào. 
                            <a href="{{ route('home') }}" class="alert-link">Khám phá ngay</a>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-12">
                                <form action="{{ route('wishlist.add-to-cart') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-shopping-cart"></i> Thêm tất cả vào giỏ hàng
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="row">
                            @foreach($wishlists as $wishlist)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <img src="{{ $wishlist->product->image ?? 'https://via.placeholder.com/300x300?text=' . urlencode($wishlist->product->name) }}" 
                                             class="card-img-top" alt="{{ $wishlist->product->name }}" style="height: 250px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $wishlist->product->name }}</h5>
                                            <p class="card-text text-truncate">{{ $wishlist->product->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="text-success font-weight-bold" style="font-size: 1.3em;">
                                                    ₫{{ number_format($wishlist->product->price, 0, ',', '.') }}
                                                </span>
                                                @if($wishlist->product->original_price > $wishlist->product->price)
                                                    <span class="badge badge-danger">
                                                        -{{ $wishlist->product->discount_percentage }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="{{ route('product.show', $wishlist->product->id) }}" class="btn btn-sm btn-primary w-100 mb-2">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                            <form action="{{ route('wishlist.remove', $wishlist->product->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                {{ $wishlists->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
