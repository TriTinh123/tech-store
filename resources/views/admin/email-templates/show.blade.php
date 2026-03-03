@extends('layouts.app')

@section('title', 'Email Template - ' . $template->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ $template->name }}</h1>
            <p class="text-muted">{{ $template->slug }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Type</h6>
                    <p class="mb-3">{{ $template->template_type }}</p>

                    <h6 class="text-muted text-uppercase">Status</h6>
                    <p class="mb-3">
                        @if($template->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>

                    <h6 class="text-muted text-uppercase">Created</h6>
                    <p class="mb-0">{{ $template->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Subject Line</h5>
                </div>
                <div class="card-body">
                    <code>{{ $template->subject }}</code>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Email Body (HTML)</h5>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
                        {!! $template->html_body !!}
                    </div>
                </div>
            </div>

            @if($template->variables)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Available Variables</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($template->variables as $variable)
                        <div class="col-md-6">
                            <code>{{ $variable }}</code>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
