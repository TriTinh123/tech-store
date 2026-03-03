@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <!-- Card Header -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-8">
                <h1 class="text-2xl font-bold text-white mb-2">
                    Trả lời câu hỏi bảo mật
                </h1>
                <p class="text-purple-100">Vui lòng trả lời tất cả các câu hỏi để xác thực</p>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <!-- User Info -->
                <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-sm text-gray-600">
                        <span class="font-semibold">Tài khoản:</span> {{ $user->email }}
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                        <p class="text-sm font-semibold text-red-700 mb-2">Lỗi xác thực</p>
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-600">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Security Questions Form -->
                <form method="POST" action="{{ route('3fa.verify-security-questions') }}" class="space-y-6">
                    @csrf

                    @foreach ($questions as $idx => $question)
                        <div>
                            <label class="block text-base font-medium text-gray-700 mb-3">
                                <span class="inline-block bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">{{ $idx + 1 }}</span>
                                {{ $question['question'] }}
                            </label>
                            <input
                                type="text"
                                name="answers[{{ $question['id'] }}]"
                                placeholder="Nhập câu trả lời của bạn..."
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition-colors {{ $errors->has("answers.{$question['id']}") ? 'border-red-500' : '' }}"
                                required
                            />
                            @error("answers.{$question['id']}")
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <!-- Info Text -->
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-600">
                            💡 <span class="font-semibold">Mẹo:</span> Câu trả lời không phân biệt chữ hoa/thường
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                    >
                        Xác thực câu trả lời
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                        ← Quay lại đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
