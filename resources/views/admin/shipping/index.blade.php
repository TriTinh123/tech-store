@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-truck"></i> Quản Lý Vận Chuyển
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Khách Hàng</th>
                                    <th>Tổng Tiền</th>
                                    <th>Mã Tracking</th>
                                    <th>Nhà Vận Chuyển</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Đặt</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>₫{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($order->tracking_number)
                                                <code>{{ $order->tracking_number }}</code>
                                            @else
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->shipping_provider)
                                                <span class="badge bg-info">{{ $order->shipping_provider }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($order->shipping_status)
                                                @case('pending')
                                                    <span class="badge bg-secondary">Chờ xử lý</span>
                                                    @break
                                                @case('processing')
                                                    <span class="badge bg-warning text-dark">Đang chuẩn bị</span>
                                                    @break
                                                @case('shipped')
                                                    <span class="badge bg-primary">Đã gửi</span>
                                                    @break
                                                @case('out_for_delivery')
                                                    <span class="badge bg-info">Đang giao</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge bg-success">Đã giao</span>
                                                    @break
                                                @case('returned')
                                                    <span class="badge bg-danger">Trả lại</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.shipping.edit', $order) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Cập Nhật
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> Không có đơn hàng nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
