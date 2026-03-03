@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-file"></i> Chi Tiết Yêu Cầu Hoàn Trả
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Mã Đơn Hàng:</label>
                                <span>#{{ $return->order->id }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Sản Phẩm:</label>
                                <span>{{ $return->orderItem->product_name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Trạng Thái:</label>
                                @if($return->status == 'pending')
                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                @elseif($return->status == 'approved')
                                    <span class="badge bg-info">Được phê duyệt</span>
                                @elseif($return->status == 'rejected')
                                    <span class="badge bg-danger">Bị từ chối</span>
                                @elseif($return->status == 'completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label style="color: #f5576c; font-weight: bold;">Hoàn Tiền:</label>
                                <span style="color: #28a745; font-size: 1.2em;">₫{{ number_format($return->refund_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label style="color: #f5576c; font-weight: bold;">Lý Do:</label>
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
                                    Tôi thay đổi ý định
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

                    @if($return->status == 'rejected' && $return->rejection_reason)
                        <div class="alert alert-danger">
                            <strong>Lý do bị từ chối:</strong><br>
                            {{ $return->rejection_reason }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ngày gửi:</strong> {{ $return->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cập nhật cuối:</strong> {{ $return->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Nếu yêu cầu được phê duyệt, tiền hoàn trả sẽ được chuyển vào tài khoản ngân hàng của bạn trong vòng 3-5 ngày làm việc.
                    </div>

                    <a href="{{ route('returns.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
