@extends('layouts.app')

@section('title', 'Create Incident')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Report New Incident</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.incidents.store') }}">
        @csrf

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Incident Title *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="severity" class="form-label">Severity *</label>
                            <select class="form-select @error('severity') is-invalid @enderror" 
                                    id="severity" name="severity" required>
                                <option value="">Select Severity</option>
                                <option value="low" {{ old('severity') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('severity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="incident_type" class="form-label">Type *</label>
                            <select class="form-select @error('incident_type') is-invalid @enderror" 
                                    id="incident_type" name="incident_type" required>
                                <option value="">Select Type</option>
                                <option value="unauthorized_access" {{ old('incident_type') == 'unauthorized_access' ? 'selected' : '' }}>Unauthorized Access</option>
                                <option value="data_breach" {{ old('incident_type') == 'data_breach' ? 'selected' : '' }}>Data Breach</option>
                                <option value="malware" {{ old('incident_type') == 'malware' ? 'selected' : '' }}>Malware</option>
                                <option value="ddos" {{ old('incident_type') == 'ddos' ? 'selected' : '' }}>DDoS Attack</option>
                                <option value="phishing" {{ old('incident_type') == 'phishing' ? 'selected' : '' }}>Phishing</option>
                            </select>
                            @error('incident_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="affected_users" class="form-label">Affected Users (comma-separated IDs) *</label>
                    <input type="text" class="form-control @error('affected_users') is-invalid @enderror" 
                           id="affected_users" name="affected_users" value="{{ old('affected_users') }}" 
                           placeholder="e.g., 1,2,3" required>
                    @error('affected_users')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create Incident
        </button>
        <a href="{{ route('admin.incidents.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
