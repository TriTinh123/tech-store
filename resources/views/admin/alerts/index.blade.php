@extends('layouts.app')

@section('title', 'Security Alerts')

@section('content')
<div class="alerts-list py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-bell"></i> Security Alerts</h1>
                <small class="text-muted">All security alerts with filtering and bulk actions</small>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.alerts.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="{{ route('admin.alerts.response-center') }}" class="btn btn-outline-primary">
                    <i class="fas fa-reply"></i> Response Center
                </a>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="needs-validation">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Title, type, user..." 
                                   value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($alertTypes as $type)
                                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Severity</label>
                            <select name="severity" class="form-select">
                                <option value="">All Severity</option>
                                <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                                <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="acknowledged" {{ request('status') === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                                <option value="under_investigation" {{ request('status') === 'under_investigation' ? 'selected' : '' }}>Investigating</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div>
                                <label class="form-label">Date Range</label>
                                <div class="input-group">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            <a href="{{ route('admin.alerts.export', ['format' => 'csv'] + request()->query()) }}" class="btn btn-outline-success">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                            <a href="{{ route('admin.alerts.export', ['format' => 'json'] + request()->query()) }}" class="btn btn-outline-info">
                                <i class="fas fa-download"></i> Export JSON
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Info -->
        @if($alerts->count() > 0)
            <div class="alert alert-info mb-3" role="alert">
                <i class="fas fa-info-circle"></i> 
                Showing {{ $alerts->firstItem() }}-{{ $alerts->lastItem() }} of {{ $alerts->total() }} alerts
                
                <form method="POST" action="{{ route('admin.alerts.bulk-action') }}" class="d-inline float-end">
                    @csrf
                    <div class="input-group" style="width: 300px;">
                        <select name="action" class="form-select form-select-sm" required>
                            <option value="">Select bulk action...</option>
                            <option value="acknowledge">Acknowledge All</option>
                            <option value="investigate">Investigate All</option>
                            <option value="resolve">Resolve All</option>
                            <option value="dismiss">Dismiss All</option>
                        </select>
                        <button class="btn btn-sm btn-primary" type="submit">Apply</button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Alerts Table -->
        <div class="card border-0 shadow-sm">
            @if($alerts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        Time <i class="fas fa-{{ request('sort') === 'created_at' && request('direction') === 'asc' ? 'arrow-up' : 'arrow-down' }} text-muted"></i>
                                    </a>
                                </th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'severity', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        Severity
                                    </a>
                                </th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $alert)
                                <tr id="row-{{ $alert->id }}">
                                    <td><input type="checkbox" class="form-check-input alert-checkbox" value="{{ $alert->id }}" data-alert-id="{{ $alert->id }}"></td>
                                    <td>
                                        <small class="text-muted">{{ $alert->created_at->format('M d, Y H:i') }}</small>
                                        <br><small>{{ $alert->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $alert->title }}</strong>
                                        <br><small class="text-muted">{{ $alert->description ? substr($alert->description, 0, 50) . '...' : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $alert->user_id) }}">
                                            {{ $alert->user?->email ?? 'Unknown' }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : ($alert->severity === 'medium' ? 'warning' : 'success')) }}">
                                            {{ ucfirst($alert->severity) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $alert->status === 'pending' ? 'warning' : ($alert->status === 'resolved' ? 'success' : 'info') }}">
                                            {{ ucfirst(str_replace('_', ' ', $alert->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.alerts.show', $alert->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                    <h4 class="mt-3">No alerts found</h4>
                    <p class="text-muted">No security alerts match your current filters.</p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($alerts->hasPages())
            <div class="mt-4">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.alert-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

document.querySelectorAll('.alert-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const allChecked = document.querySelectorAll('.alert-checkbox:checked').length === document.querySelectorAll('.alert-checkbox').length;
        document.getElementById('selectAll').checked = allChecked;
    });
});
</script>

<style>
    table.table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
</style>
@endsection
