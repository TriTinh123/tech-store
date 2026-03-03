@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Security Questions</h1>
                    <p class="text-gray-600 mt-1">Set up security questions for additional account protection</p>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <!-- Setup Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form method="POST" action="{{ route('3fa.setup-questions') }}" class="space-y-8">
                @csrf

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <span class="font-semibold">ℹ️ Important:</span> Please answer at least 2 security questions. These will be used to verify your identity during future logins.
                    </p>
                </div>

                <!-- Question 1 -->
                <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200">
                    <div class="mb-4">
                        <label for="question_1" class="block text-sm font-semibold text-gray-900 mb-2">
                            Security Question 1 <span class="text-red-600">*</span>
                        </label>
                        <select
                            name="question_1"
                            id="question_1"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('question_1') border-red-500 @enderror"
                            required
                        >
                            <option value="">Select a question...</option>
                            @foreach($questions as $q)
                                <option value="{{ $q->id }}" @selected(old('question_1') == $q->id)>
                                    {{ $q->question }}
                                </option>
                            @endforeach
                        </select>
                        @error('question_1')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="answer_1" class="block text-sm font-semibold text-gray-900 mb-2">
                            Your Answer <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            name="answer_1"
                            id="answer_1"
                            value="{{ old('answer_1') }}"
                            placeholder="Enter your answer"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('answer_1') border-red-500 @enderror"
                            autocomplete="off"
                        >
                        @error('answer_1')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Answer is case-insensitive</p>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200">
                    <div class="mb-4">
                        <label for="question_2" class="block text-sm font-semibold text-gray-900 mb-2">
                            Security Question 2 <span class="text-red-600">*</span>
                        </label>
                        <select
                            name="question_2"
                            id="question_2"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('question_2') border-red-500 @enderror"
                            required
                        >
                            <option value="">Select a question...</option>
                            @foreach($questions as $q)
                                <option value="{{ $q->id }}" @selected(old('question_2') == $q->id)>
                                    {{ $q->question }}
                                </option>
                            @endforeach
                        </select>
                        @error('question_2')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="answer_2" class="block text-sm font-semibold text-gray-900 mb-2">
                            Your Answer <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            name="answer_2"
                            id="answer_2"
                            value="{{ old('answer_2') }}"
                            placeholder="Enter your answer"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('answer_2') border-red-500 @enderror"
                            autocomplete="off"
                        >
                        @error('answer_2')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Answer is case-insensitive</p>
                    </div>
                </div>

                <!-- Question 3 (Optional) -->
                <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200">
                    <div class="mb-4">
                        <label for="question_3" class="block text-sm font-semibold text-gray-900 mb-2">
                            Security Question 3 <span class="text-gray-400">(Optional)</span>
                        </label>
                        <select
                            name="question_3"
                            id="question_3"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('question_3') border-red-500 @enderror"
                        >
                            <option value="">Select a question...</option>
                            @foreach($questions as $q)
                                <option value="{{ $q->id }}" @selected(old('question_3') == $q->id)>
                                    {{ $q->question }}
                                </option>
                            @endforeach
                        </select>
                        @error('question_3')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="answer_3" class="block text-sm font-semibold text-gray-900 mb-2">
                            Your Answer
                        </label>
                        <input
                            type="text"
                            name="answer_3"
                            id="answer_3"
                            value="{{ old('answer_3') }}"
                            placeholder="Enter your answer"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors @error('answer_3') border-red-500 @enderror"
                            autocomplete="off"
                        >
                        @error('answer_3')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Answer is case-insensitive</p>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->has('questions'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ $errors->first('questions') }}
                    </div>
                @endif

                <!-- Submit Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button
                        type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
                    >
                        Save Security Questions
                    </button>
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-3 px-4 rounded-lg transition-colors duration-200 text-center"
                    >
                        Skip for Now
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Section -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Why Security Questions?</h3>
            <ul class="space-y-3 text-gray-600">
                <li class="flex items-start">
                    <span class="text-blue-600 mr-3">✓</span>
                    <span>Additional verification layer for your account security</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-3">✓</span>
                    <span>Only you know the answers to your personal questions</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-3">✓</span>
                    <span>Required during 3FA verification at login</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-3">✓</span>
                    <span>Answers are hashed and stored securely</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
