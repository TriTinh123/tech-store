@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-undo"></i> Quản Lý Yêu Cầu Hoàn Trả
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Đơn Hàng</th>
                                    <th>Sản Phẩm</th>
                                    <th>Lý Do</th>
                                    <th>Hoàn Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Yêu Cầu</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                    <tr>
                                        <td><strong>#{{ $return->order->id }}</strong></td>
                                        <td>{{ Str::limit($return->orderItem->product_name, 40) }}</td>
                                        <td>
                                            @switch($return->reason)
                                                @case('defective')
                                                    <span class="badge bg-danger">Sản phẩm bị lỗi</span>
                                                    @break
                                                @case('wrong_item')
                                                    <span class="badge bg-warning">Gửi nhầm</span>
                                                    @break
                                                @case('not_as_described')
                                                    <span class="badge bg-warning">Không đúng mô tả</span>
                                                    @break
                                                @case('changed_mind')
                                                    <span class="badge bg-info">Thay đổi ý định</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $return->reason }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <strong style="color: #f5576c;">₫{{ number_format($return->refund_amount, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @if($return->status === 'pending')
                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                            @elseif($return->status === 'approved')
                                                <span class="badge bg-success">Đã phê duyệt</span>
                                            @elseif($return->status === 'rejected')
                                                <span class="badge bg-danger">Bị từ chối</span>
                                            @elseif($return->status === 'completed')
                                                <span class="badge bg-info">Hoàn thành</span>
                                            @endif
                                        </td>
                                        <td>{{ $return->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.returns.show', $return) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Chi Tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> Không có yêu cầu hoàn trả nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            {{ $returns->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
