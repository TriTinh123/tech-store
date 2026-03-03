@extends('layouts.app')

@section('title', 'Create Email Template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Create Email Template</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.email-templates.store') }}">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Template Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (unique)</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug') }}" required>
                            @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="template_type" class="form-label">Type</label>
                            <select class="form-select @error('template_type') is-invalid @enderror" 
                                    id="template_type" name="template_type" required>
                                <option value="">Select Type</option>
                                <option value="alert" {{ old('template_type') == 'alert' ? 'selected' : '' }}>Alert</option>
                                <option value="notification" {{ old('template_type') == 'notification' ? 'selected' : '' }}>Notification</option>
                                <option value="verification" {{ old('template_type') == 'verification' ? 'selected' : '' }}>Verification</option>
                                <option value="report" {{ old('template_type') == 'report' ? 'selected' : '' }}>Report</option>
                                <option value="invitation" {{ old('template_type') == 'invitation' ? 'selected' : '' }}>Invitation</option>
                            </select>
                            @error('template_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject Line</label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                           id="subject" name="subject" value="{{ old('subject') }}" required>
                    @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="html_body" class="form-label">Email Body (HTML)</label>
                    <textarea class="form-control @error('html_body') is-invalid @enderror" 
                              id="html_body" name="html_body" rows="15" required>{{ old('html_body') }}</textarea>
                    @error('html_body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Use @{{ variable_name }} for template variables
                    </small>
                </div>

                <div class="mb-3">
                    <label for="text_body" class="form-label">Email Body (Text)</label>
                    <textarea class="form-control @error('text_body') is-invalid @enderror" 
                              id="text_body" name="text_body" rows="10">{{ old('text_body') }}</textarea>
                    @error('text_body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" 
                               name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (use in system)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create Template
        </button>
        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
