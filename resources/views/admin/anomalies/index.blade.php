@extends('layouts.app')

@section('title', 'Login Anomalies')

@section('content')
<div class="anomalies-list py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-list"></i> Login Anomalies</h1>
                <small class="text-muted">All detected login anomalies and suspicious activity</small>
            </div>
            <a href="{{ route('admin.anomalies.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Search & Filter Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="needs-validation">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Email, IP, type..." 
                                   value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($anomalyTypes as $type)
                                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Risk Level</label>
                            <select name="risk_level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="critical" {{ request('risk_level') === 'critical' ? 'selected' : '' }}>Critical</option>
                                <option value="high" {{ request('risk_level') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ request('risk_level') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ request('risk_level') === 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                                <option value="under_investigation" {{ request('status') === 'under_investigation' ? 'selected' : '' }}>Under Investigation</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
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
                            <a href="{{ route('admin.anomalies.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            <a href="{{ route('admin.anomalies.export', ['format' => 'csv'] + request()->query()) }}" class="btn btn-outline-success">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                            <a href="{{ route('admin.anomalies.export', ['format' => 'json'] + request()->query()) }}" class="btn btn-outline-info">
                                <i class="fas fa-download"></i> Export JSON
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Info -->
        @if($anomalies->count() > 0)
            <div class="alert alert-info mb-3" role="alert">
                <i class="fas fa-info-circle"></i> 
                Showing {{ $anomalies->firstItem() }}-{{ $anomalies->lastItem() }} of {{ $anomalies->total() }} anomalies
            </div>
        @endif

        <!-- Anomalies Table -->
        <div class="card border-0 shadow-sm">
            @if($anomalies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        Time <i class="fas fa-{{ request('sort') === 'created_at' && request('direction') === 'asc' ? 'arrow-up' : 'arrow-down' }} text-muted"></i>
                                    </a>
                                </th>
                                <th>User</th>
                                <th>Type</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'ip_address', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        IP Address
                                    </a>
                                </th>
                                <th>Location</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'risk_level', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        Risk Level
                                    </a>
                                </th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anomalies as $anomaly)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ $anomaly->created_at->format('M d, Y H:i') }}</small>
                                        <br><small>{{ $anomaly->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $anomaly->user_id) }}">
                                            {{ $anomaly->user?->email ?? 'Unknown' }}
                                        </a>
                                        <br><small class="text-muted">{{ $anomaly->user?->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            @switch($anomaly->anomaly_type)
                                                @case('new_location')
                                                    <i class="fas fa-map-marker-alt"></i> New Location
                                                @break
                                                @case('new_device')
                                                    <i class="fas fa-mobile-alt"></i> New Device
                                                @break
                                                @case('unusual_time')
                                                    <i class="fas fa-clock"></i> Unusual Time
                                                @break
                                                @case('failed_login')
                                                    <i class="fas fa-times-circle"></i> Failed Login
                                                @break
                                                @case('multiple_failed')
                                                    <i class="fas fa-exclamation"></i> Multiple Failed
                                                @break
                                                @default
                                                    {{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $anomaly->ip_address }}</small>
                                        @if($anomaly->is_whitelisted)
                                            <br><span class="badge bg-success">Whitelisted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($anomaly->country)
                                            <i class="fas fa-map-pin"></i> {{ $anomaly->country }}
                                            @if($anomaly->city)
                                                , {{ $anomaly->city }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $anomaly->risk_level === 'critical' ? 'danger' : ($anomaly->risk_level === 'high' ? 'warning' : ($anomaly->risk_level === 'medium' ? 'warning' : 'success')) }}">
                                            {{ ucfirst($anomaly->risk_level) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $anomaly->status === 'new' ? 'info' : ($anomaly->status === 'resolved' ? 'success' : ($anomaly->status === 'blocked' ? 'danger' : 'warning')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $anomaly->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.anomalies.show', $anomaly->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$anomaly->is_whitelisted)
                                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#resolveModal{{ $anomaly->id }}" title="Resolve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Resolve Modal -->
                                <div class="modal fade" id="resolveModal{{ $anomaly->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admin.anomalies.resolve', $anomaly->id) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Resolve Anomaly #{{ $anomaly->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Action</label>
                                                        <select name="action" class="form-select" required>
                                                            <option value="">Choose action...</option>
                                                            <option value="whitelist">Whitelist (Trusted)</option>
                                                            <option value="investigate">Investigate</option>
                                                            <option value="block">Block IP</option>
                                                            <option value="resolve">Mark as Resolved</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Notes</label>
                                                        <textarea name="notes" class="form-control" rows="3" placeholder="Add notes..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                    <h4 class="mt-3">No anomalies found</h4>
                    <p class="text-muted">Great! No suspicious login activity detected with current filters.</p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($anomalies->hasPages())
            <div class="mt-4">
                {{ $anomalies->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    table.table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-group {
        gap: 2px;
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
</style>
@endsection
