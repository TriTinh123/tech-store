@extends('layouts.app')

@section('title', 'Email Templates')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Email Templates</h1>
            <p class="text-muted">Manage system email templates</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Template
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                    <tr>
                        <td>
                            <strong>{{ $template->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $template->template_type }}</span>
                        </td>
                        <td>{{ Str::limit($template->subject, 50) }}</td>
                        <td>
                            @if($template->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.email-templates.show', $template->id) }}" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.email-templates.edit', $template->id) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.email-templates.preview', $template->id) }}" 
                               target="_blank" class="btn btn-sm btn-secondary">
                                <i class="fas fa-search"></i>
                            </a>
                            <form action="{{ route('admin.email-templates.test', $template->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                                <button type="submit" class="btn btn-sm btn-warning" 
                                        onclick="return confirm('Send test email?')">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.email-templates.destroy', $template->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Delete this template?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No email templates found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    {{ $templates->links() }}
</div>
@endsection
