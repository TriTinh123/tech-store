@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-plus"></i> Thêm Sản Phẩm Mới
                    </h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Kiểm tra lại:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-4">
                            <label for="name" class="form-label"><i class="fas fa-tag"></i> Tên Sản Phẩm</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="slug" class="form-label"><i class="fas fa-link"></i> Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug') }}" required>
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="category_id" class="form-label"><i class="fas fa-list"></i> Danh Mục</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="description" class="form-label"><i class="fas fa-align-left"></i> Mô Tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="price" class="form-label"><i class="fas fa-money-bill"></i> Giá Bán</label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="original_price" class="form-label"><i class="fas fa-tag"></i> Giá Gốc (nếu có)</label>
                                    <input type="number" step="0.01" class="form-control @error('original_price') is-invalid @enderror" 
                                           id="original_price" name="original_price" value="{{ old('original_price') }}">
                                    @error('original_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="stock" class="form-label"><i class="fas fa-box"></i> Tồn Kho</label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                           id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="image" class="form-label"><i class="fas fa-image"></i> URL Hình Ảnh</label>
                                    <input type="text" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" value="{{ old('image') }}" placeholder="/images/product-name.jpg" oninput="previewImage()">
                                    <small class="text-muted">VD: /images/CorsairK95.jpg</small>
                                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group mb-4">
                                    <label for="image_file" class="form-label"><i class="fas fa-upload"></i> Hoặc Upload Ảnh</label>
                                    <input type="file" class="form-control @error('image_file') is-invalid @enderror" 
                                           id="image_file" name="image_file" accept="image/*" onchange="previewImageFile()">
                                    <small class="text-muted">JPG, PNG, GIF (Max 2MB)</small>
                                    @error('image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group mb-4">
                                    <label class="form-label"><i class="fas fa-eye"></i> Preview Ảnh</label>
                                    <div style="border: 2px dashed #e9ecef; border-radius: 8px; padding: 20px; text-align: center; min-height: 200px; background: #f8f9fa;">
                                        <img id="imagePreview" src="https://via.placeholder.com/200x200?text=No+Image" 
                                             style="max-width: 100%; max-height: 200px; border-radius: 5px;" alt="Preview">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="manufacturer" class="form-label"><i class="fas fa-industry"></i> Nhà Sản Xuất</label>
                            <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" 
                                   id="manufacturer" name="manufacturer" value="{{ old('manufacturer') }}">
                            @error('manufacturer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                       value="1" @if(old('is_featured')) checked @endif>
                                <label class="form-check-label" for="is_featured">
                                    <i class="fas fa-star"></i> Sản phẩm nổi bật
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Thêm Sản Phẩm
                            </button>
                            <a href="{{ route('admin.products') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-weight: 600;
        color: #667eea;
        margin-bottom: 8px;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 15px;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>

<script>
    function previewImage() {
        const imageInput = document.getElementById('image').value.trim();
        const imagePreview = document.getElementById('imagePreview');
        
        if (imageInput && imageInput.length > 0) {
            // Add cache buster with updated timestamp
            const cacheBuster = '?v=' + new Date().getTime();
            const imageSrc = imageInput.includes('?') ? imageInput + '&v=' + new Date().getTime() : imageInput + cacheBuster;
            
            imagePreview.src = imageSrc;
            imagePreview.style.opacity = '0.5';
            imagePreview.style.transition = 'opacity 0.3s ease-in-out';
            
            imagePreview.onload = function() {
                this.style.opacity = '1';
            };
            
            imagePreview.onerror = function() {
                this.src = 'https://via.placeholder.com/200x200?text=Invalid+Image';
                this.style.opacity = '1';
            };
        } else {
            imagePreview.src = 'https://via.placeholder.com/200x200?text=No+Image';
            imagePreview.style.opacity = '1';
        }
    }

    function previewImageFile() {
        const fileInput = document.getElementById('image_file');
        const imagePreview = document.getElementById('imagePreview');
        
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.opacity = '1';
            };
            
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
    
    // Run preview on page load
    document.addEventListener('DOMContentLoaded', previewImage);
    
    // Real-time preview as user types
    document.getElementById('image').addEventListener('input', previewImage);
    document.getElementById('image').addEventListener('keyup', previewImage);
</script>
@endsection
