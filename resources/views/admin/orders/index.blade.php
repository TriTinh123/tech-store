@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h1 style="color: #667eea; font-weight: bold;">
        <i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng
    </h1>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="color: #667eea; font-weight: bold;">ID</th>
                                <th style="color: #667eea; font-weight: bold;">Khách Hàng</th>
                                <th style="color: #667eea; font-weight: bold;">Tổng Tiền</th>
                                <th style="color: #667eea; font-weight: bold;">Trạng Thái</th>
                                <th style="color: #667eea; font-weight: bold;">Ngày</th>
                                <th style="color: #667eea; font-weight: bold;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                                    <td><strong style="color: #48bb78;">₫{{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                        @elseif($order->status == 'confirmed')
                                            <span class="badge bg-info">Xác nhận</span>
                                        @elseif($order->status == 'shipped')
                                            <span class="badge bg-primary">Đang giao</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge bg-success">Đã giao</span>
                                        @else
                                            <span class="badge bg-danger">Hủy</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Chi Tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="alert alert-info">Chưa có đơn hàng</div>
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
