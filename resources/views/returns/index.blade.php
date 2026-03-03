@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-list"></i> Lịch Sử Yêu Cầu Hoàn Trả
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($returns->count() == 0)
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Bạn chưa có yêu cầu hoàn trả nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background-color: #f8f9fa; border-top: 3px solid #f5576c;">
                                    <tr>
                                        <th>Mã Đơn Hàng</th>
                                        <th>Sản Phẩm</th>
                                        <th>Lý Do</th>
                                        <th>Trạng Thái</th>
                                        <th>Hoàn Tiền</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returns as $return)
                                        <tr>
                                            <td><strong>#{{ $return->order->id }}</strong></td>
                                            <td>{{ $return->orderItem->product_name }}</td>
                                            <td>
                                                @switch($return->reason)
                                                    @case('defective')
                                                        Sản phẩm bị lỗi
                                                        @break
                                                    @case('wrong_item')
                                                        Gửi nhầm sản phẩm
                                                        @break
                                                    @case('not_as_described')
                                                        Không đúng mô tả
                                                        @break
                                                    @case('changed_mind')
                                                        Thay đổi ý định
                                                        @break
                                                    @default
                                                        {{ $return->reason }}
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($return->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                                @elseif($return->status == 'approved')
                                                    <span class="badge bg-info">Được phê duyệt</span>
                                                @elseif($return->status == 'rejected')
                                                    <span class="badge bg-danger">Bị từ chối</span>
                                                @elseif($return->status == 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong style="color: #f5576c;">₫{{ number_format($return->refund_amount, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('returns.show', $return->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Chi Tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                {{ $returns->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
