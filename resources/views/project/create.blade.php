@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('master.project.index') }}" class="btn btn-outline p-2 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Project</h1>
                <p class="text-gray-600 dark:text-gray-400">Masukkan data project baru ke dalam sistem</p>
            </div>
        </div>
    </div>

    <!-- Project Form -->
    <div class="max-w-4xl">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Project</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('master.project.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="form-label">Nama Project <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <input type="text" name="name" id="name" class="form-input pl-10 @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('name') }}" required placeholder="Masukkan nama project">
                            </div>
                            @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="project_type" class="form-label">Jenis Project <span class="text-red-500">*</span></label>
                            <select name="project_type" id="project_type" class="form-input @error('project_type') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" required>
                                <option value="">Pilih Jenis Project</option>
                                @foreach($projectTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('project_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" class="form-input @error('status') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="on_progress" {{ old('status') == 'on_progress' ? 'selected' : '' }}>On Progress</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="company" class="form-label">Company <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <input type="text" name="company" id="company" class="form-input pl-10 @error('company') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('company') }}" placeholder="Masukkan nama company" required>
                            </div>
                            @error('company')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="location" class="form-label flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243a8 8 0 0011.314-11.314l-4.243-4.243a4 4 0 00-5.657 5.657l4.243 4.243z"></path>
                                </svg>
                                Lokasi Project <span class="text-red-500">*</span>
                            </label>
                            <select name="location" id="location" class="form-input select2-modern @error('location') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" required style="width:100%">
                                <option value="">Pilih Kota Project...</option>
                            </select>
                            @error('location')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                        <div>
                            <label for="start_date" class="form-label">Start Date <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-input @error('start_date') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('start_date') }}" required>
                            @error('start_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="form-label">End Date <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-input @error('end_date') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('end_date') }}" required>
                            @error('end_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="location" class="form-label flex items-center">
                                Kategori<span class="text-red-500">*</span>
                            </label>
                            <select name="category" id="category" class="form-input select2-modern @error('category') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" required style="width:100%">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Internal" {{ old('category', $data->category ?? '') == 'Internal' ? 'selected' : '' }}>Internal</option>
                                <option value="External" {{ old('category', $data->category ?? '') == 'External' ? 'selected' : '' }}>External</option>
                            </select>
                            @error('category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="space-y-3">
                        <label for="description" class="form-label flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Deskripsi Project
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <textarea
                                        name="description"
                                        id="description"
                                        rows="5"
                                        class="w-full bg-transparent border-0 focus:ring-0 resize-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('description') text-red-600 dark:text-red-400 @enderror"
                                        placeholder="Jelaskan detail project, lokasi, skala, dan informasi penting lainnya...">{{ old('description') }}</textarea>
                                </div>
                                <div class="px-4 pb-3 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Deskripsi opsional
                                    </span>
                                    <span id="char-count" class="font-mono">0 karakter</span>
                                </div>
                            </div>
                        </div>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('master.project.index') }}" class="btn btn-outline flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Simpan Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('description');
        const charCount = document.getElementById('char-count');

        function updateCharCount() {
            const count = textarea.value.length;
            charCount.textContent = count + ' karakter';

            // Change color based on length
            if (count > 500) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-gray-400', 'text-yellow-500');
            } else if (count > 200) {
                charCount.classList.add('text-yellow-500');
                charCount.classList.remove('text-gray-400', 'text-red-500');
            } else {
                charCount.classList.add('text-gray-400');
                charCount.classList.remove('text-yellow-500', 'text-red-500');
            }
        }

        textarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count
    });
</script>
@endsection

@push('styles')
<style>
    .select2-container--default .select2-selection--single {
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        min-height: 44px;
        padding: 8px 12px;
        font-size: 1rem;
        color: #111827;
        transition: border 0.2s;
    }

    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single.select2-selection--focus {
        border-color: #2563eb;
        outline: none;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #111827;
        line-height: 28px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }

    .select2-dropdown {
        border-radius: 0.5rem;
        box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.08);
    }

    .select2-results__option {
        padding-left: 2.5rem;
        position: relative;
    }

    .select2-results__option .city-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #2563eb;
    }
</style>
@endpush

@push('scripts')
<script data-selected-location="{{ old('location') }}">
    // Menggunakan global cities data
    $(function() {
        const select = $('#location');
        const oldLocation = $('script[data-selected-location]').attr('data-selected-location');

        // Setup location select menggunakan helper function global
        window.setupLocationSelect(select, oldLocation);
    });
</script>
@endpush