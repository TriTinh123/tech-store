@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-ticket-alt"></i> Quản Lý Mã Giảm Giá
                    </h3>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Tạo Mới
                    </a>
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
                                    <th>Mã</th>
                                    <th>Loại</th>
                                    <th>Giá Trị</th>
                                    <th>Tối Thiểu</th>
                                    <th>Ngày Hết Hạn</th>
                                    <th>Sử Dụng</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td><strong>{{ $coupon->code }}</strong></td>
                                        <td>
                                            @if($coupon->type === 'fixed')
                                                <span class="badge bg-primary">Cố định</span>
                                            @else
                                                <span class="badge bg-success">Phần trăm</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->type === 'fixed')
                                                ₫{{ number_format($coupon->value, 0, ',', '.') }}
                                            @else
                                                {{ $coupon->value }}%
                                            @endif
                                        </td>
                                        <td>₫{{ number_format($coupon->min_order_amount, 0, ',', '.') }}</td>
                                        <td>{{ $coupon->valid_to->format('d/m/Y') }}</td>
                                        <td>
                                            @if($coupon->usage_limit)
                                                {{ $coupon->used_count }}/{{ $coupon->usage_limit }}
                                            @else
                                                {{ $coupon->used_count }}/∞
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->is_active && $coupon->isValid())
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            @else
                                                <span class="badge bg-danger">Không hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.coupons.delete', $coupon) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> Không có mã giảm giá nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            {{ $coupons->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
