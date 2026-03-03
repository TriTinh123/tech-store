@extends('layouts.app')

@section('title', 'Chi Tiết Phiên')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            ← Quay Lại
        </a>
        <h1 class="h3 mb-0">🔍 Chi Tiết Phiên Đăng Nhập</h1>
    </div>

    <!-- Session Information -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📱 Thông Tin Thiết Bị</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Loại Thiết Bị</small>
                        <p class="mb-0">
                            @if($session->device_type === 'mobile')
                                📱 Mobile
                            @elseif($session->device_type === 'tablet')
                                📱 Tablet
                            @else
                                💻 Desktop
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Trình Duyệt</small>
                        <p class="mb-0">{{ $session->browser ?? 'Unknown' }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Hệ Điều Hành</small>
                        <p class="mb-0">{{ $session->os ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <small class="text-muted">User Agent</small>
                        <p class="mb-0"><code class="text-break" style="font-size: 11px;">{{ $session->user_agent }}</code></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">🌍 Thông Tin Vị Trí & IP</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">IP Address</small>
                        <p class="mb-0"><code>{{ $session->ip_address }}</code></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Vị Trí</small>
                        <p class="mb-0">{{ $session->location ?? 'Unknown' }}</p>
                    </div>
                    @if($session->latitude && $session->longitude)
                        <div class="mb-3">
                            <small class="text-muted">Tọa Độ</small>
                            <p class="mb-0">{{ $session->latitude }}, {{ $session->longitude }}</p>
                        </div>
                    @endif
                    <div>
                        <small class="text-muted">Device Fingerprint</small>
                        <p class="mb-0"><code class="text-break" style="font-size: 11px;">{{ $session->device_fingerprint }}</code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Timeline -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">⏱️ Thời Gian Phiên</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Bắt Đầu</small>
                            <p class="mb-0"><strong>{{ $session->created_at->format('d/m/Y H:i:s') }}</strong></p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Hoạt Động Lần Cuối</small>
                            <p class="mb-0">
                                <strong>{{ $session->last_activity_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</strong>
                                @if($session->last_activity_at)
                                    <br>
                                    <small class="text-muted">({{ $session->last_activity_at->diffForHumans() }})</small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Kết Thúc</small>
                            <p class="mb-0">
                                @if($session->logged_out_at)
                                    <strong>{{ $session->logged_out_at->format('d/m/Y H:i:s') }}</strong>
                                @else
                                    <span class="badge bg-success">Còn Hoạt Động</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Thời Lượng</small>
                            <p class="mb-0"><strong>{{ $duration }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Status -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📊 Trạng Thái Phiên</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Trạng Thái</small>
                            <p class="mb-0">
                                @if($session->is_active && !$session->logged_out_at)
                                    <span class="badge bg-success">✅ Hoạt Động</span>
                                @else
                                    <span class="badge bg-secondary">⏸️ Không Hoạt Động</span>
                                @endif
                                @if($isCurrent)
                                    <span class="badge bg-primary">Phiên Hiện Tại</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Tin Cậy</small>
                            <p class="mb-0">
                                @if($session->is_trusted ?? false)
                                    <span class="badge bg-success">🛡️ Tin Cậy</span>
                                @else
                                    <span class="badge bg-warning">⚠️ Chưa Xác Nhận</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Gắn Cờ</small>
                            <p class="mb-0">
                                @if($session->is_flagged)
                                    <span class="badge bg-danger">🚩 Đáng Ngờ</span>
                                    @if($session->flag_reason)
                                        <br>
                                        <small class="text-muted">{{ $session->flag_reason }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-success">✓ Bình Thường</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Log for This Session -->
    @if($activities->count() > 0)
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">📝 Hoạt Động Trong Phiên ({{ $activities->count() }})</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hành Động</th>
                                        <th>Mô Tả</th>
                                        <th>Phương Thức</th>
                                        <th>Trạng Thái</th>
                                        <th>Thời Gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $activity)
                                        <tr>
                                            <td><small><strong>{{ $activity->action }}</strong></small></td>
                                            <td><small class="text-muted">{{ Str::limit($activity->description, 50) }}</small></td>
                                            <td><small><span class="badge bg-secondary">{{ $activity->method }}</span></small></td>
                                            <td>
                                                @if($activity->status_code >= 200 && $activity->status_code < 300)
                                                    <small class="text-success">{{ $activity->status_code }}</small>
                                                @elseif($activity->status_code >= 400)
                                                    <small class="text-danger">{{ $activity->status_code }}</small>
                                                @else
                                                    <small class="text-warning">{{ $activity->status_code }}</small>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    @if(!$isCurrent && $session->is_active)
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('sessions.terminate', $session->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn kết thúc phiên này?')">
                        🚪 Kết Thúc Phiên
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
