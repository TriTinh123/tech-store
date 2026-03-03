@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-file-invoice"></i> Chi Tiết Yêu Cầu Hoàn Trả
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Mã Đơn Hàng:</label>
                                <span>#{{ $return->order->id }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Khách Hàng:</label>
                                <span>{{ $return->order->customer_name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Sản Phẩm:</label>
                                <span>{{ $return->orderItem->product_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Hoàn Tiền:</label>
                                <span style="color: #28a745; font-size: 1.1em;">₫{{ number_format($return->refund_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label style="color: #f5576c; font-weight: bold;">Lý Do Hoàn Trả:</label>
                        <p>
                            @switch($return->reason)
                                @case('defective')
                                    Sản phẩm bị lỗi/hư hỏng
                                    @break
                                @case('wrong_item')
                                    Gửi nhầm sản phẩm
                                    @break
                                @case('not_as_described')
                                    Sản phẩm không đúng mô tả
                                    @break
                                @case('changed_mind')
                                    Khách hàng thay đổi ý định
                                    @break
                                @default
                                    {{ $return->reason }}
                            @endswitch
                        </p>
                    </div>

                    <div class="mb-4">
                        <label style="color: #f5576c; font-weight: bold;">Mô Tả Chi Tiết:</label>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 3px solid #f5576c;">
                            {{ $return->description }}
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Ngày Yêu Cầu:</strong> {{ $return->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Trạng Thái:</strong>
                                @if($return->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                @elseif($return->status === 'approved')
                                    <span class="badge bg-success">Đã phê duyệt</span>
                                @elseif($return->status === 'rejected')
                                    <span class="badge bg-danger">Bị từ chối</span>
                                @elseif($return->status === 'completed')
                                    <span class="badge bg-info">Hoàn thành</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($return->status === 'rejected' && $return->rejection_reason)
                        <div class="alert alert-danger">
                            <strong>Lý Do Từ Chối:</strong><br>
                            {{ $return->rejection_reason }}
                        </div>
                    @endif

                    @if($return->status === 'pending')
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Xử Lý Yêu Cầu</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.returns.approve', $return) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-block mb-2">
                                                <i class="fas fa-check"></i> Phê Duyệt Hoàn Trả
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times"></i> Từ Chối
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($return->status === 'approved')
                        <form action="{{ route('admin.returns.complete', $return) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Đánh Dấu Hoàn Thành
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.returns') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay Lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Từ Chối Yêu Cầu Hoàn Trả</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.returns.reject', $return) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason" class="form-label">Lý Do Từ Chối *</label>
                        <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="4" 
                                  placeholder="Vui lòng nhập lý do từ chối..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Từ Chối</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
