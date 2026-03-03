@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-exchange-alt"></i> So Sánh Sản Phẩm
                    </h3>
                </div>
                <div class="card-body">
                    @if(count($products) == 0)
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Bạn chưa chọn sản phẩm nào để so sánh. 
                            <a href="{{ route('home') }}" class="alert-link">Quay lại trang chủ</a>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-12">
                                <form action="{{ route('comparison.clear') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Xóa tất cả
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th style="width: 20%;">Thuộc tính</th>
                                        @foreach($products as $product)
                                            <th style="width: 26.7%;">{{ $product->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Hình ảnh</strong></td>
                                        @foreach($products as $product)
                                            <td>
                                                <img src="{{ $product->image ?? 'https://via.placeholder.com/150x150?text=' . urlencode($product->name) }}" 
                                                     alt="{{ $product->name }}" style="max-width: 150px; height: auto;">
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Giá</strong></td>
                                        @foreach($products as $product)
                                            <td>
                                                <span class="text-success font-weight-bold">₫{{ number_format($product->price, 0, ',', '.') }}</span>
                                                @if($product->original_price > $product->price)
                                                    <br><del class="text-muted">₫{{ number_format($product->original_price, 0, ',', '.') }}</del>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Tồn kho</strong></td>
                                        @foreach($products as $product)
                                            <td>
                                                @if($product->stock > 0)
                                                    <span class="badge badge-success">{{ $product->stock }} sản phẩm</span>
                                                @else
                                                    <span class="badge badge-danger">Hết hàng</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Rating</strong></td>
                                        @foreach($products as $product)
                                            <td>
                                                @for($i = 0; $i < 5; $i++)
                                                    @if($i < ($product->rating ?? 0))
                                                        <i class="fas fa-star" style="color: #ffc107;"></i>
                                                    @else
                                                        <i class="far fa-star" style="color: #ffc107;"></i>
                                                    @endif
                                                @endfor
                                                <small>({{ $product->reviews_count ?? 0 }} đánh giá)</small>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Danh mục</strong></td>
                                        @foreach($products as $product)
                                            <td>{{ $product->category }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Mô tả</strong></td>
                                        @foreach($products as $product)
                                            <td>
                                                <small>{{ Str::limit($product->description, 100) }}</small>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Hành động</strong></td>
                                        @foreach($products as $product)
                                            <td>
                                                <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-primary mb-2 w-100">
                                                    <i class="fas fa-eye"></i> Xem chi tiết
                                                </a>
                                                <form action="{{ route('comparison.remove', $product->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning w-100">
                                                        <i class="fas fa-minus"></i> Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
