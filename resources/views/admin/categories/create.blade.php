@extends('layouts.admin')

@section('title', 'Create Category')

@section('body-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="margin: 0; color: #1a202c; font-weight: 700;">Create Category</h2>
    <a href="{{ route('admin.categories') }}" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Category Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter category name" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter category description" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">{{ old('description') }}</textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn" style="background: #3b82f6; color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-save"></i> Create Category
                </button>
                <a href="{{ route('admin.categories') }}" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: 600;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
