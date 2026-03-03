@extends('layouts.app')

@section('title', 'Device Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Your Devices</h1>
            <p class="text-muted">Manage and monitor your trusted devices</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('security.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Security
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Current Device -->
    @php
        $currentDevice = $devices->first();
    @endphp
    @if($currentDevice)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-success border-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h5 class="card-title d-flex align-items-center">
                                <i class="fas fa-laptop me-2 text-success"></i>
                                {{ $currentDevice->device_name }}
                                <span class="badge bg-success ms-2">Current Device</span>
                            </h5>
                            <p class="text-muted mb-2">
                                <strong>OS:</strong> {{ $currentDevice->os }}<br>
                                <strong>Browser:</strong> {{ $currentDevice->browser }}<br>
                                <strong>Last Used:</strong> {{ $currentDevice->last_used_at->diffForHumans() }}<br>
                                <strong>IP Address:</strong> <code>{{ $currentDevice->ip_address }}</code>
                            </p>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="badge bg-light text-dark">{{ $currentDevice->device_type }}</span><br>
                            @if($currentDevice->is_trusted)
                                <span class="badge bg-success mt-2">Trusted</span>
                            @else
                                <span class="badge bg-secondary mt-2">Not Trusted</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Other Devices -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">Other Devices ({{ $devices->count() - 1 }})</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($devices->skip(1) as $device)
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <i class="fas fa-{{ $device->device_type == 'mobile' ? 'mobile-alt' : 'laptop' }} text-muted"></i>
                                {{ $device->device_name }}
                            </h6>
                            <p class="text-muted mb-2 small">
                                <strong>Type:</strong> {{ ucfirst($device->device_type) }}<br>
                                <strong>OS:</strong> {{ $device->os }}<br>
                                <strong>Browser:</strong> {{ $device->browser }}<br>
                                <strong>IP Address:</strong> <code>{{ $device->ip_address }}</code><br>
                                <strong>Last Used:</strong> {{ $device->last_used_at->diffForHumans() }}
                            </p>
                            <div>
                                @if($device->is_trusted)
                                    <span class="badge bg-success">Trusted</span>
                                @else
                                    <span class="badge bg-secondary">Not Trusted</span>
                                @endif
                            </div>
                        </div>
                        <div class="ms-3">
                            @if(!$device->is_trusted)
                            <form method="POST" action="{{ route('security.device.trust', $device->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as trusted">
                                    <i class="fas fa-check"></i> Trust
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('security.device.untrust', $device->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Mark as untrusted">
                                    <i class="fas fa-times"></i> Untrust
                                </button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('security.device.remove', $device->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Remove this device?')" title="Remove device">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No other devices registered</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Device Management Tips -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-lightbulb"></i> Device Management Best Practices</h5>
                <ul class="mb-0">
                    <li>Mark devices you use regularly as "Trusted" for faster access</li>
                    <li>Untrust or remove devices you no longer use</li>
                    <li>Be cautious with devices on public networks</li>
                    <li>Review your devices list regularly</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
