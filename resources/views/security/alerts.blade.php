@extends('layouts.app')

@section('title', 'Security Alerts')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Security Alerts</h1>
            <p class="text-muted">Review all security-related alerts</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('security.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Security
            </a>
        </div>
    </div>

    <!-- Unread Alerts Count -->
    @php
        $unreadCount = $alerts->filter(fn($a) => $a->read_at === null)->count();
    @endphp
    @if($unreadCount > 0)
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fas fa-bell"></i> You have {{ $unreadCount }} unread alert(s)
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @forelse($alerts as $alert)
            <div class="p-3 border-bottom d-flex justify-content-between align-items-start 
                        {{ !$alert->read_at ? 'bg-light' : '' }}">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h6 class="mb-0">{{ $alert->alert_type }}</h6>
                        <span class="badge bg-{{ $alert->severity == 'critical' ? 'danger' : ($alert->severity == 'high' ? 'warning' : ($alert->severity == 'medium' ? 'info' : 'secondary')) }}">
                            {{ ucfirst($alert->severity) }}
                        </span>
                        @if(!$alert->read_at)
                        <span class="badge bg-primary">New</span>
                        @endif
                    </div>

                    <p class="mb-2 text-dark">{{ $alert->message }}</p>

                    <div class="row g-2 small text-muted mb-2">
                        <div class="col-auto">
                            <i class="fas fa-clock"></i> {{ $alert->created_at->diffForHumans() }}
                        </div>
                        @if($alert->ip_address)
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt"></i> {{ $alert->ip_address }}
                        </div>
                        @endif
                        @if($alert->location)
                        <div class="col-auto">
                            <i class="fas fa-globe"></i> {{ $alert->location }}
                        </div>
                        @endif
                    </div>

                    @if($alert->description)
                    <p class="mb-0 text-muted small">{{ $alert->description }}</p>
                    @endif
                </div>

                <div class="ms-3">
                    @if(!$alert->read_at)
                    <form method="POST" action="{{ route('security.alert.read', $alert->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check"></i> Mark as Read
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="fas fa-check-circle fa-3x mb-2 opacity-50"></i>
                <p>No security alerts</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($alerts->hasPages())
    <div class="mt-4">
        {{ $alerts->links() }}
    </div>
    @endif

    <!-- Alert Severity Guide -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Alert Severity Levels</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div>
                                <span class="badge bg-danger">Critical</span>
                                <small class="d-block mt-1 text-muted">Immediate action required</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div>
                                <span class="badge bg-warning">High</span>
                                <small class="d-block mt-1 text-muted">Very important</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div>
                                <span class="badge bg-info">Medium</span>
                                <small class="d-block mt-1 text-muted">Should review soon</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div>
                                <span class="badge bg-secondary">Low</span>
                                <small class="d-block mt-1 text-muted">Informational</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
