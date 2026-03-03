@extends('layouts.app')

@section('title', 'Edit Email Template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Edit Template</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.email-templates.show', $template->id) }}" class="btn btn-secondary">
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

    <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject Line</label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                           id="subject" name="subject" value="{{ old('subject', $template->subject) }}" required>
                    @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="html_body" class="form-label">Email Body (HTML)</label>
                    <textarea class="form-control @error('html_body') is-invalid @enderror" 
                              id="html_body" name="html_body" rows="15" required>{{ old('html_body', $template->html_body) }}</textarea>
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
                              id="text_body" name="text_body" rows="10">{{ old('text_body', $template->text_body) }}</textarea>
                    @error('text_body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" 
                               name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (use in system)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
            <div class="col-md-6">
                <a href="{{ route('admin.email-templates.preview', $template->id) }}" 
                   target="_blank" class="btn btn-secondary w-100">
                    <i class="fas fa-eye"></i> Preview
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
