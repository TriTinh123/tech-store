@extends('layouts.admin')

@section('title', 'Create Product')

@section('body-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="margin: 0; color: #1a202c; font-weight: 700;">Create Product</h2>
    <a href="{{ route('admin.products') }}" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Product Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter product name" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Slug *</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="product-slug" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter product description" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">{{ old('description') }}</textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Category</label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Manufacturer</label>
                    <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer') }}" placeholder="Manufacturer name" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Price *</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required min="0" step="0.01" placeholder="0.00" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Original Price</label>
                    <input type="number" name="original_price" class="form-control" value="{{ old('original_price') }}" min="0" step="0.01" placeholder="0.00" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Stock *</label>
                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock') }}" required min="0" placeholder="0" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                    @error('stock') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; color: #2d3748; display: block; margin-bottom: 0.5rem;">Product Image</label>
                <input type="file" name="image_file" class="form-control @error('image_file') is-invalid @enderror" accept="image/*" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 4px;">
                @error('image_file') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_featured" class="form-check-input" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label" style="color: #2d3748;">Featured Product</label>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn" style="background: #3b82f6; color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-save"></i> Create Product
                </button>
                <a href="{{ route('admin.products') }}" class="btn" style="background: #e2e8f0; color: #2d3748; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: 600;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
