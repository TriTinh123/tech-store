@extends('layouts.app')

@section('title', 'Lịch Sử Hoạt Động')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            ← Quay Lại
        </a>
        <h1 class="h3 mb-0">📊 Lịch Sử Hoạt Động</h1>
        <p class="text-muted small">Xem toàn bộ hoạt động của tài khoản bạn</p>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $stats['total_activities'] ?? 0 }}</h4>
                    <small class="text-muted">Tổng Hoạt Động</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-info">{{ $stats['unique_ips'] ?? 0 }}</h4>
                    <small class="text-muted">IP Duy Nhất</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $stats['unique_devices'] ?? 0 }}</h4>
                    <small class="text-muted">Thiết Bị Duy Nhất</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-warning" id="failedCount">0</h4>
                    <small class="text-muted">Đăng Nhập Thất Bại</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Methods Chart -->
    @if(isset($stats['activities_by_method']) && count($stats['activities_by_method']) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">📈 Hoạt Động Theo Phương Thức</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($stats['activities_by_method'] as $method => $count)
                        <div class="col-md-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">{{ $method }}</small>
                                <span class="badge bg-secondary">{{ $count }}</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" style="width: {{ ($count / ($stats['total_activities'] ?? 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Activity Timeline -->
    @if($activities->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">📅 Lịch Sử Chi Tiết ({{ $activities->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="timeline">
                    @foreach($activities as $activity)
                        <div class="timeline-item pb-4">
                            <div class="d-flex">
                                <!-- Timeline dot -->
                                <div class="timeline-dot">
                                    <div class="rounded-circle" style="width: 12px; height: 12px; background-color: {{ $this->getActionColor($activity->action) }}; margin: 3px 0;"></div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1 ms-3">
                                    <!-- Action -->
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">{{ $activity->action }}</h6>
                                            <small class="text-muted">{{ $activity->description }}</small>
                                        </div>
                                        <small class="text-muted text-nowrap ms-2">{{ $activity->created_at->format('H:i') }}</small>
                                    </div>

                                    <!-- Details -->
                                    <small class="text-muted">
                                        {{ $activity->method }} • {{ $activity->url }} • 
                                        @if($activity->status_code >= 200 && $activity->status_code < 300)
                                            <span class="text-success">{{ $activity->status_code }}</span>
                                        @elseif($activity->status_code >= 400)
                                            <span class="text-danger">{{ $activity->status_code }}</span>
                                        @else
                                            {{ $activity->status_code }}
                                        @endif
                                    </small>

                                    <!-- IP Info -->
                                    <div class="mt-2">
                                        <small class="d-block text-muted">
                                            <code>{{ $activity->ip_address }}</code>
                                        </small>
                                    </div>

                                    <hr class="my-3">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <strong>ℹ️ Thông tin:</strong> Chưa có dữ liệu hoạt động.
        </div>
    @endif
</div>

<style>
    .timeline-dot {
        display: flex;
        justify-content: center;
        width: 30px;
        flex-shrink: 0;
    }

    .timeline-item {
        border-left: 2px solid #e9ecef;
        padding-left: 0;
    }

    .timeline-item:last-child {
        border-left: none;
    }
</style>

@endsection

@php
if (!function_exists('getActionColor')) {
    function getActionColor($action) {
        return match (true) {
            str_contains($action, 'LOGIN') => '#28a745',
            str_contains($action, 'LOGOUT') => '#6c757d',
            str_contains($action, 'DELETE') => '#dc3545',
            str_contains($action, 'UPDATE') => '#ffc107',
            str_contains($action, 'CREATE') => '#17a2b8',
            default => '#007bff',
        };
    }
}
@endphp
