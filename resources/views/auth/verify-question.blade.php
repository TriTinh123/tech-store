@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Security Question</h2>
            <p class="text-gray-600">Answer your security question</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Question Form -->
        <form method="POST" action="{{ route('3fa.verify-question') }}" class="space-y-6">
            @csrf

            <!-- Question Display -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm font-medium text-blue-600 uppercase">Question</p>
                <p class="text-lg font-semibold text-gray-900 mt-2">{{ $question->question }}</p>
            </div>

            <!-- Hidden Question ID -->
            <input type="hidden" name="question_id" value="{{ $question->id }}">

            <!-- Answer Input -->
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                    Your Answer
                </label>
                <input
                    type="text"
                    name="answer"
                    id="answer"
                    value="{{ old('answer') }}"
                    placeholder="Enter your answer"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('answer') border-red-500 @enderror"
                    autofocus
                    required
                    autocomplete="off"
                >
                <p class="text-xs text-gray-500 mt-1">Answer is case-insensitive</p>
            </div>

            <!-- Security Note -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
                <p class="font-medium mb-1">💡 Tip:</p>
                <p>Your answer is stored securely and compared case-insensitively.</p>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
            >
                Verify Answer
            </button>
        </form>

        <!-- Cancel Link -->
        <div class="text-center mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('3fa.cancel') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                Cancel verification
            </a>
        </div>
    </div>
</div>
@endsection
