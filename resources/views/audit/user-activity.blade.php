{{-- @var \App\Models\User $user --}}
@extends('layouts.app')
@php
    /** @var \App\Models\User $user */
@endphp

@section('title', 'User Activity - ' . $user->name)

@section('content')
<div class="user-activity-container py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-user-circle"></i> User Activity</h1>
                <p class="text-muted mb-0">{{ $user->name }} ({{ $user->email }})</p>
            </div>
            <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>

        <!-- User Statistics -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-list fa-2x text-primary mb-3"></i>
                        <h5>{{ number_format($userStats['totalActions']) }}</h5>
                        <small class="text-muted">Total Actions</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <h5>{{ number_format($userStats['successfulActions']) }}</h5>
                        <small class="text-muted">Successful</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x text-danger mb-3"></i>
                        <h5>{{ number_format($userStats['failedActions']) }}</h5>
                        <small class="text-muted">Failed</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h5>{{ number_format($userStats['suspiciousActions']) }}</h5>
                        <small class="text-muted">Suspicious</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Activity -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-hourglass-end"></i> Last Activity</h5>
                    </div>
                    <div class="card-body">
                        @if($userStats['lastActivity'])
                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">Action</h6>
                                <p class="mb-0">
                                    <span class="badge bg-light text-dark" style="font-size: 14px;">
                                        {{ $userStats['lastActivity']->action }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">Time</h6>
                                <p class="mb-0">
                                    {{ $userStats['lastActivity']->created_at->format('Y-m-d H:i:s') }}<br>
                                    <small class="text-muted">{{ $userStats['lastActivity']->created_at->diffForHumans() }}</small>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">Status</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $userStats['lastActivity']->status === 'success' ? 'success' : 'danger' }}">
                                        {{ ucfirst($userStats['lastActivity']->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">IP Address</h6>
                                <p class="mb-0"><code>{{ $userStats['lastActivity']->ip_address }}</code></p>
                            </div>
                        </div>
                        @else
                        <p class="text-muted mb-0">No activity recorded</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Activity Timeline</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>IP Address</th>
                                        <th>Country</th>
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
                                            <span class="badge bg-light text-dark">{{ $log->action }}</span>
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($log->description, 40) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code style="font-size: 11px;">{{ $log->ip_address }}</code>
                                        </td>
                                        <td>
                                            {{ $log->country ?? 'Unknown' }}
                                        </td>
                                        <td>
                                            @if($log->is_suspicious)
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                            @else
                                                <span class="badge bg-success">✓</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('audit.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            No activity logs found
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-activity-container {
    background-color: #f8f9fa;
}

.stat-card {
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
