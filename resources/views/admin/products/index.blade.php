@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 style="color: #667eea; font-weight: bold;">
                <i class="fas fa-box"></i> Quản Lý Sản Phẩm
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Thêm Sản Phẩm
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="color: #667eea; font-weight: bold;">Sản Phẩm</th>
                                <th style="color: #667eea; font-weight: bold;">Danh Mục</th>
                                <th style="color: #667eea; font-weight: bold;">Giá</th>
                                <th style="color: #667eea; font-weight: bold;">Tồn Kho</th>
                                <th style="color: #667eea; font-weight: bold;">Nổi Bật</th>
                                <th style="color: #667eea; font-weight: bold;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->image)
                                                <img src="{{ $product->image }}" style="width: 40px; height: 40px; border-radius: 4px; margin-right: 10px; object-fit: cover;" />
                                            @endif
                                            <div>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small class="text-muted">ID: {{ $product->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->categoryModel)
                                            <span class="badge bg-info">{{ $product->categoryModel->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong style="color: #48bb78;">₫{{ number_format($product->price, 0, ',', '.') }}</strong><br>
                                        @if($product->original_price)
                                            <small class="text-muted"><s>₫{{ number_format($product->original_price, 0, ',', '.') }}</s></small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge @if($product->stock > 0) bg-success @else bg-danger @endif">
                                            {{ $product->stock }} {{ $product->stock > 0 ? 'cái' : 'Hết' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->is_featured)
                                            <span class="badge bg-warning">⭐ Nổi bật</span>
                                        @else
                                            <span class="badge bg-light text-dark">Bình thường</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.products.delete', $product) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có sản phẩm nào. <a href="{{ route('admin.products.create') }}">Tạo sản phẩm mới</a>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
        </a>
    </div>
</div>
@endsection
