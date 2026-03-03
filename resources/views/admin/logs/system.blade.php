@extends('layouts.app')

@section('title', 'Nhật ký hoạt động hệ thống')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fas fa-log"></i> Nhật ký hoạt động hệ thống
            </h1>
            <small class="text-muted">Theo dõi tất cả các hoạt động trên hệ thống</small>
        </div>
    </div>

    @if($logs->count() > 0)
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">Hành động</th>
                            <th style="width: 30%">Mô tả</th>
                            <th style="width: 15%">Người dùng</th>
                            <th style="width: 20%">Thời gian</th>
                            <th style="width: 15%">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $logs->firstItem() + $loop->index }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $log->action }}</span>
                                </td>
                                <td>
                                    {{ $log->description }}
                                </td>
                                <td>
                                    @if($log->user)
                                        <strong>{{ $log->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $log->user->email }}</small>
                                    @else
                                        <span class="text-danger">Người dùng đã xóa</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-clock me-2 text-primary"></i>
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->data)
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" 
                                                data-bs-target="#dataModal{{ $log->id }}">
                                            <i class="fas fa-eye"></i> Xem
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="dataModal{{ $log->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Chi tiết dữ liệu</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <pre class="bg-light p-3 rounded"><code>{{ $log->data }}</code></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
            <i class="fas fa-info-circle"></i> Chưa có nhật ký hoạt động nào.
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
    pre {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endsection
