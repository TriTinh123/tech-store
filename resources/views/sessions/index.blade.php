@extends('layouts.app')

@section('title', 'Quản Lý Phiên Đăng Nhập')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">🔐 Quản Lý Phiên Đăng Nhập</h1>
                <p class="text-muted small">Xem và quản lý các phiên đăng nhập hoạt động của bạn</p>
            </div>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#terminateAllModal">
                🚪 Kết Thúc Tất Cả Phiên Khác
            </button>
        </div>
    </div>

    <!-- Session Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $activeSessions->count() }}</h4>
                    <small class="text-muted">Phiên Hoạt Động</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-info">{{ $inactiveSessions->count() }}</h4>
                    <small class="text-muted">Phiên Không Hoạt Động</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-success" id="trustedDeviceCount">-</h4>
                    <small class="text-muted">Thiết Bị Tin Cậy</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-warning" id="uniqueLocations">-</h4>
                    <small class="text-muted">Vị Trí Đăng Nhập</small>
                </div>
            </div>
        </div>
    </div>

    @if($activeSessions->count() > 0)
        <!-- Active Sessions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">✅ Phiên Hoạt Động ({{ $activeSessions->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Thiết Bị</th>
                                <th>Vị Trí</th>
                                <th>IP Address</th>
                                <th>Hoạt Động Lần Cuối</th>
                                <th>Đăng Nhập</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeSessions as $session)
                                <tr>
                                    <td>
                                        <div>
                                            @if($session->device_type === 'mobile')
                                                📱
                                            @elseif($session->device_type === 'tablet')
                                                📱
                                            @else
                                                💻
                                            @endif
                                            <strong>{{ $session->device_type }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $session->browser }} on {{ $session->os }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $session->location ?? 'Unknown' }}</small>
                                    </td>
                                    <td>
                                        <small><code>{{ $session->ip_address }}</code></small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $session->last_activity_at?->diffForHumans() ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $session->created_at->diffForHumans() }}</small>
                                        @if($session->is_current)
                                            <br>
                                            <span class="badge bg-success">Hiện Tại</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                            Chi Tiết
                                        </a>
                                        @if(!$session->is_current)
                                            <form action="{{ route('sessions.terminate', $session->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn?')">
                                                    Kết Thúc
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <strong>ℹ️ Thông tin:</strong> Không có phiên hoạt động. Bạn chỉ có phiên hiện tại.
        </div>
    @endif

    <!-- Inactive Sessions -->
    @if($inactiveSessions->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">⏸️ Phiên Gần Đây ({{ $inactiveSessions->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Thiết Bị</th>
                                <th>Vị Trí</th>
                                <th>IP Address</th>
                                <th>Kết Thúc</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inactiveSessions as $session)
                                <tr class="opacity-75">
                                    <td>
                                        <small>{{ $session->device_type }} ({{ $session->browser }})</small>
                                    </td>
                                    <td>
                                        <small>{{ $session->location ?? 'Unknown' }}</small>
                                    </td>
                                    <td>
                                        <small><code>{{ $session->ip_address }}</code></small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $session->logged_out_at?->diffForHumans() ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                            Chi Tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-6">
            <a href="{{ route('sessions.trusted-devices') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center py-4">
                    <h5 class="text-primary mb-2">🛡️ Thiết Bị Tin Cậy</h5>
                    <p class="text-muted small mb-0">Quản lý danh sách thiết bị tin cậy</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('sessions.activity-timeline') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center py-4">
                    <h5 class="text-primary mb-2">📊 Lịch Sử Hoạt Động</h5>
                    <p class="text-muted small mb-0">Xem tất cả hoạt động của bạn</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Terminate All Modal -->
<div class="modal fade" id="terminateAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🚪 Kết Thúc Tất Cả Phiên Khác</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn sắp kết thúc <strong>tất cả phiên đăng nhập khác</strong>.</p>
                <p class="text-warning mb-0"><strong>⚠️ Chú ý:</strong> Bạn sẽ bị đăng xuất từ tất cả các thiết bị khác. Phiên hiện tại sẽ được giữ lại.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('sessions.terminate-others') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Kết Thúc Tất Cả</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Load statistics
    fetch('{{ route("sessions.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('trustedDeviceCount').textContent = data.unique_devices;
            document.getElementById('uniqueLocations').textContent = data.unique_locations;
        });
</script>
@endsection
