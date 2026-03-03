@extends('layouts.app')

@section('title', 'Lịch sử đăng nhập')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fas fa-history"></i> Lịch sử đăng nhập
            </h1>
            <small class="text-muted">Theo dõi tất cả các lần đăng nhập vào hệ thống</small>
        </div>
    </div>

    @if($logs->count() > 0)
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 20%">Người dùng</th>
                            <th style="width: 25%">Email</th>
                            <th style="width: 20%">IP Address</th>
                            <th style="width: 20%">Thời gian</th>
                            <th style="width: 10%">User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $logs->firstItem() + $loop->index }}</span>
                                </td>
                                <td>
                                    @if($log->user)
                                        <strong>{{ $log->user->name }}</strong>
                                    @else
                                        <span class="text-danger">Người dùng đã xóa</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->user)
                                        <a href="mailto:{{ $log->user->email }}">{{ $log->user->email }}</a>
                                    @else
                                        <span class="text-danger">-</span>
                                    @endif
                                </td>
                                <td>
                                    <code class="text-dark">{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        {{ $log->login_at->setTimezone(config('app.timezone'))->format('d/m/Y H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" 
                                            title="{{ $log->user_agent }}">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Hiển thị {{ $logs->firstItem() }} đến {{ $logs->lastItem() }} trong {{ $logs->total() }} bản ghi
                    </small>
                </div>
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </nav>
    @else
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> Chưa có lịch sử đăng nhập nào.
        </div>
    @endif
</div>

<style>
    .table thead {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.9em;
    }
</style>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
