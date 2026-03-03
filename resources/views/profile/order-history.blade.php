@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-shopping-bag"></i> Lịch Sử Đơn Hàng
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($orders->count() == 0)
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Bạn chưa có đơn hàng nào. 
                            <a href="{{ route('home') }}" class="alert-link">Tiếp tục mua sắm</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background-color: #f8f9fa; border-top: 3px solid #11998e;">
                                    <tr>
                                        <th style="color: #11998e; font-weight: bold;">
                                            <i class="fas fa-hashtag"></i> Mã Đơn Hàng
                                        </th>
                                        <th style="color: #11998e; font-weight: bold;">
                                            <i class="fas fa-calendar"></i> Ngày Đặt
                                        </th>
                                        <th style="color: #11998e; font-weight: bold;">
                                            <i class="fas fa-box"></i> Số Lượng
                                        </th>
                                        <th style="color: #11998e; font-weight: bold;">
                                            <i class="fas fa-money-bill"></i> Tổng Tiền
                                        </th>
                                        <th style="color: #11998e; font-weight: bold;">
                                            <i class="fas fa-info-circle"></i> Trạng Thái
                                        </th>
                                        <th style="color: #11998e; font-weight: bold;">
                                            <i class="fas fa-actions"></i> Hành Động
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr class="order-row" style="transition: all 0.3s ease;">
                                            <td>
                                                <strong>#{{ $order->id }}</strong>
                                            </td>
                                            <td>
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $order->items_count ?? $order->items()->count() }} sản phẩm</span>
                                            </td>
                                            <td>
                                                <strong style="color: #38ef7d; font-size: 1.1em;">
                                                    ₫{{ number_format($order->total_amount, 0, ',', '.') }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($order->status == 'pending')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock"></i> Đang xử lý
                                                    </span>
                                                @elseif($order->status == 'confirmed')
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-check"></i> Đã xác nhận
                                                    </span>
                                                @elseif($order->status == 'shipped')
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-truck"></i> Đang giao
                                                    </span>
                                                @elseif($order->status == 'delivered')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> Đã giao
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle"></i> Hủy
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('profile.order-detail', $order->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Chi Tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($orders->hasPages())
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    {{-- Previous Page Link --}}
                                    @if ($orders->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link">← Trước</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $orders->previousPageUrl() }}">← Trước</a></li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                        @if ($page == $orders->currentPage())
                                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($orders->hasMorePages())
                                        <li class="page-item"><a class="page-link" href="{{ $orders->nextPageUrl() }}">Tiếp → </a></li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link">Tiếp → </span></li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    @endif

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay Lại Hồ Sơ
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Tiếp Tục Mua Sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table {
        font-size: 0.95rem;
    }

    .order-row:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.8rem;
        border-radius: 6px;
    }

    .btn-sm {
        transition: all 0.3s ease;
    }

    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 3px solid #11998e;
    }

    .pagination .page-link {
        color: #11998e;
        border-color: #dee2e6;
    }

    .pagination .page-link:hover {
        color: #fff;
        background-color: #11998e;
        border-color: #11998e;
    }

    .pagination .page-item.active .page-link {
        background-color: #11998e;
        border-color: #11998e;
    }
</style>
@endsection
