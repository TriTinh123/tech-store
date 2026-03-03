@extends('layouts.app')

@section('title', 'Thiết Bị Tin Cậy')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            ← Quay Lại
        </a>
        <h1 class="h3 mb-0">🛡️ Thiết Bị Tin Cậy</h1>
        <p class="text-muted small">Quản lý các thiết bị bạn tin cậy</p>
    </div>

    @if(count($devices) > 0)
        <div class="row">
            @foreach($devices as $item)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <!-- Device Icon -->
                            <div class="mb-3">
                                @if($item['device']->device_type === 'mobile')
                                    <span class="h2">📱</span>
                                @elseif($item['device']->device_type === 'tablet')
                                    <span class="h2">📱</span>
                                @else
                                    <span class="h2">💻</span>
                                @endif
                            </div>

                            <!-- Device Info -->
                            <h5 class="card-title mb-3">{{ ucfirst($item['device']->device_type) }}</h5>

                            <div class="mb-3">
                                <small class="text-muted">Trình Duyệt</small>
                                <p class="mb-2"><strong>{{ $item['device']->browser }}</strong></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Hệ Điều Hành</small>
                                <p class="mb-2"><strong>{{ $item['device']->os }}</strong></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">IP Address</small>
                                <p class="mb-2"><code>{{ $item['device']->ip_address }}</code></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Vị Trí</small>
                                <p class="mb-2">{{ $item['device']->location ?? 'Unknown' }}</p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Số Phiên</small>
                                <p class="mb-2"><span class="badge bg-secondary">{{ $item['session_count'] }} phiên</span></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Lần Cuối Sử Dụng</small>
                                <p class="mb-0">{{ $item['device']->created_at->diffForHumans() }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 pt-3 border-top">
                                @if($item['device']->is_trusted ?? false)
                                    <form action="{{ route('sessions.remove-trust', $item['device']->device_fingerprint) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            🔓 Loại Bỏ Tin Cậy
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('sessions.mark-trusted', $item['device']->device_fingerprint) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            🔒 Đánh Dấu Là Tin Cậy
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Device Security Tips -->
        <div class="alert alert-info mt-4">
            <h6 class="alert-heading">💡 Mẹo Cho Thiết Bị Tin Cậy</h6>
            <ul class="mb-0">
                <li>Chỉ đánh dấu thiết bị cá nhân là "Tin Cậy"</li>
                <li>Nếu mất hoặc chia sẻ thiết bị, hãy loại bỏ tin cậy</li>
                <li>Thiết bị tin cậy có thể bỏ qua một số xác thực bổ sung</li>
                <li>Định kỳ xem xét danh sách thiết bị để bảo mật</li>
            </ul>
        </div>
    @else
        <div class="alert alert-info">
            <strong>ℹ️ Thông tin:</strong> Bạn chưa có thiết bị tin cậy nào. Thiết bị sẽ được thêm vào sau khi bạn đăng nhập thành công.
        </div>
    @endif
</div>
@endsection
