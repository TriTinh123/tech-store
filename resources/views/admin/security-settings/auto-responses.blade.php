@extends('layouts.app')

@section('title', 'Phản ứng tự động')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Phản ứng tự động</h1>
            <p class="mt-2 text-gray-600">Theo dõi và quản lý các phản ứng tự động đối với hoạt động đáng ngờ</p>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Tổng phản ứng</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_responses'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Chờ xử lý</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Đã thực thi</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['executed'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Thất bại</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Tỷ lệ thành công</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['success_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <!-- Responses Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Người dùng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trigger
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hành động
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mức độ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thời gian
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hành động
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($responses as $response)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $response->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500">{{ $response->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $response->getTriggerTypeLabel() }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $response->getResponseActionLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($response->severity === 'critical') bg-red-100 text-red-800
                                        @elseif($response->severity === 'high') bg-orange-100 text-orange-800
                                        @elseif($response->severity === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $response->getSeverityLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($response->status === 'executed') bg-green-100 text-green-800
                                        @elseif($response->status === 'failed') bg-red-100 text-red-800
                                        @elseif($response->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $response->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $response->triggered_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.security-settings.auto-response-detail', $response->id) }}" class="text-blue-600 hover:text-blue-900">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Không có phản ứng tự động nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($responses->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    {{ $responses->links() }}
                </div>
            @endif
        </div>

        <!-- Action Type Distribution -->
        @if (!empty($stats['by_action']))
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- By Action Type -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Theo loại hành động</h3>
                <div class="space-y-2">
                    @foreach ($stats['by_action'] as $action => $count)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $action }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- By Severity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Theo mức độ</h3>
                <div class="space-y-2">
                    @foreach ($stats['by_severity'] as $severity => $count)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ ucfirst($severity) }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- By Trigger Type -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Theo loại kích hoạt</h3>
                <div class="space-y-2">
                    @foreach ($stats['by_trigger_type'] as $trigger => $count)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $trigger }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
