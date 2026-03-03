@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="audit-logs-container py-5">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-list"></i> Audit Logs</h1>
                <small class="text-muted">Browse and search all system logs</small>
            </div>
            <div class="btn-group" role="group">
                <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <a href="{{ route('audit.filter') }}" class="btn btn-outline-primary">
                    <i class="fas fa-filter"></i> Advanced Filter
                </a>
                <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('audit.export-csv', request()->query()) }}">Export as CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('audit.export-json', request()->query()) }}">Export as JSON</a></li>
                </ul>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3" id="filterForm">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" placeholder="Search logs..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select class="form-select" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="suspicious" value="true" id="suspiciousCheck" {{ request('suspicious') === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="suspiciousCheck">
                                Show Suspicious Activity Only
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Information -->
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i>
            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
        </div>

        <!-- Logs Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ route('audit.index', array_merge(request()->query(), ['sort' => 'created_at', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none">
                                        Timestamp @if(request('sort') === 'created_at') <i class="fas fa-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i> @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('audit.index', array_merge(request()->query(), ['sort' => 'user_id', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none">
                                        User @if(request('sort') === 'user_id') <i class="fas fa-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i> @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('audit.index', array_merge(request()->query(), ['sort' => 'action', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none">
                                        Action @if(request('sort') === 'action') <i class="fas fa-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i> @endif
                                    </a>
                                </th>
                                <th>Description</th>
                                <th>
                                    <a href="{{ route('audit.index', array_merge(request()->query(), ['sort' => 'status', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none">
                                        Status @if(request('sort') === 'status') <i class="fas fa-arrow-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i> @endif
                                    </a>
                                </th>
                                <th>IP Address</th>
                                <th>Suspicious</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $log->user?->name ?? 'Unknown' }}</strong><br>
                                    <small class="text-muted">{{ $log->user?->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <small>{{ Str::limit($log->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>
                                    <code>{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    @if($log->is_suspicious)
                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Yes</span>
                                    @else
                                        <span class="badge bg-success">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('audit.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    No audit logs found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav>
            {{ $logs->links() }}
        </nav>
    </div>
</div>

<style>
.audit-logs-container {
    background-color: #f8f9fa;
}

.table-responsive {
    min-height: 400px;
}
</style>
@endsection
