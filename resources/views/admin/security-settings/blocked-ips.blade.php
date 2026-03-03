@extends('layouts.app')

@section('title', 'Quản lý IP bị chặn')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quản lý IP bị chặn</h1>
                <p class="mt-2 text-gray-600">Xem và quản lý các địa chỉ IP bị chặn</p>
            </div>
            <button onclick="openBlockIpModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Chặn IP mới
            </button>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Tổng IP bị chặn</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['currently_active'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Chặn vĩnh viễn</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['permanent_blocks'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Chặn tạm thời</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['temporary_blocks'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Nguy cấp</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['critical_risk'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Blocked IPs Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vị trí
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loại chặn
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mức độ rủi ro
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thời gian chặn
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hành động
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($blockedIps as $ip)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono font-medium text-gray-900">{{ $ip->ip_address }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $ip->location ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $ip->country_code ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($ip->block_type === 'manual') bg-blue-100 text-blue-800
                                        @elseif($ip->block_type === 'auto_attack') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $ip->getBlockTypeLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($ip->risk_level === 'critical') bg-red-100 text-red-800
                                        @elseif($ip->risk_level === 'high') bg-orange-100 text-orange-800
                                        @elseif($ip->risk_level === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $ip->getRiskLevelLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($ip->is_permanent)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Vĩnh viễn
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $ip->getRemainingBlockTime() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.security-settings.blocked-ip-detail', $ip->ip_address) }}" class="text-blue-600 hover:text-blue-900">
                                        Chi tiết
                                    </a>
                                    <form action="{{ route('admin.security-settings.unblock-ip', $ip->ip_address) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bạn chắc chắn muốn bỏ chặn IP này?')">
                                            Bỏ chặn
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Không có IP nào bị chặn
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($blockedIps->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    {{ $blockedIps->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Block IP Modal -->
    <div id="blockIpModal" class="hidden fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Chặn IP mới</h2>
            </div>

            <form action="{{ route('admin.security-settings.block-ip') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-4">
                    <label for="ip_address" class="block text-sm font-medium text-gray-700">
                        Địa chỉ IP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="ip_address" name="ip_address" placeholder="192.168.1.1" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700">
                        Lý do chặn
                    </label>
                    <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập lý do chặn..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="is_permanent" class="flex items-center">
                        <input type="checkbox" id="is_permanent" name="is_permanent" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Chặn vĩnh viễn</span>
                    </label>
                </div>

                <div class="mb-6">
                    <label for="block_duration_minutes" class="block text-sm font-medium text-gray-700">
                        Thời gian chặn (phút) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="block_duration_minutes" name="block_duration_minutes" min="5" max="10080" value="60" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBlockIpModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Chặn IP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openBlockIpModal() {
    document.getElementById('blockIpModal').classList.remove('hidden');
}

function closeBlockIpModal() {
    document.getElementById('blockIpModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('blockIpModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBlockIpModal();
    }
});
</script>
@endsection
