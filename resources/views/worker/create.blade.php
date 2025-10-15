@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('master.worker.index') }}" class="btn btn-outline p-2 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Worker</h1>
                <p class="text-gray-600 dark:text-gray-400">Add a new worker to the system</p>
            </div>
        </div>
    </div>

    <!-- Notification Messages -->
    @if(session('success'))
    <div class="mb-6">
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6">
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Worker Form -->
    <div class="max-w-4xl">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Worker Information</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('master.worker.store') }}" method="POST" class="space-y-6" id="workerForm">
                    @csrf

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="form-label">Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="name" id="name" class="form-input pl-10 @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('name') }}" required placeholder="Enter worker name">
                            </div>
                            @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit" class="form-label">Unit <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="unit" id="unit" class="form-input pl-10 @error('unit') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('unit') }}" required placeholder="e.g., OH, Person, Day">
                            </div>
                            @error('unit')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Input Baru  -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="Kewarganegaraan" class="form-label">Kewarganegaraan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="Kewarganegaraan" id="Kewarganegaraan" class="form-input select2 pl-10 @error('Kewarganegaraan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                    <option value="">Pilih Kewarganegaraan</option>
                                    <option value="WNI">WNI</option>
                                    <option value="WNA">WNA</option>
                                </select>
                            </div>
                            @error('Kewarganegaraan')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kualifikasi" class="form-label">Kualifikasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="kualifikasi" id="kualifikasi" class="form-input pl-10 @error('kualifikasi') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('kualifikasi') }}" placeholder="Optional">
                            </div>
                            @error('kualifikasi')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category_id" class="form-label">Category <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <select name="category_id" id="category_id" class="form-input select2 pl-10 @error('category_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="classification_tkdn" class="form-label">Klasifikasi TKDN <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <select name="classification_tkdn" id="classification_tkdn" class="form-input pl-10 select2 @error('classification_tkdn') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" required>
                                    <option value="">Pilih Klasifikasi TKDN...</option>
                                    @foreach(\App\Models\Worker::getClassificationOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('classification_tkdn') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('classification_tkdn')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="location" class="form-label">Location <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243a8 8 0 0011.314-11.314l-4.243-4.243a4 4 0 00-5.657 5.657l4.243 4.243z"></path>
                                    </svg>
                                </div>
                                <select name="location" id="location" class="form-input pl-10 @error('location') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" required>
                                    <option value="">Pilih Kota Worker...</option>
                                </select>
                            </div>
                            @error('location')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="price" class="form-label">Price (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <input type="text" name="price" id="price" class="form-input pl-10 @error('price') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('price') ? number_format(old('price'), 0, ',', '.') : '' }}" required placeholder="Enter hourly rate">
                            </div>
                            @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tkdn" class="form-label">TKDN (%) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="tkdn" id="tkdn" class="form-input pl-10 @error('tkdn') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" value="{{ old('tkdn') }}" required placeholder="Enter TKDN percentage" step="0.01">
                            </div>
                            @error('tkdn')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('master.worker.index') }}" class="btn btn-outline flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Worker
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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

        // Price formatting dengan pemisah titik
        const priceInput = $('#price');

        // Format angka saat input
        priceInput.on('input', function() {
            let value = this.value.replace(/[^\d]/g, ''); // Hapus semua karakter kecuali angka

            if (value) {
                // Format dengan pemisah titik setiap 3 digit
                value = parseInt(value).toLocaleString('id-ID');
                this.value = value;
            }
        });

        // Format angka saat focus out (untuk memastikan format yang benar)
        priceInput.on('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');

            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
                this.value = value;
            }
        });

        // Format angka saat focus in (hapus pemisah untuk editing)
        priceInput.on('focus', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = value;
            }
        });

        // Handle form submit - hapus pemisah titik sebelum submit
        $('#workerForm').on('submit', function(e) {
            const priceValue = priceInput.val();
            if (priceValue) {
                // Hapus semua karakter kecuali angka sebelum submit
                const cleanValue = priceValue.replace(/[^\d]/g, '');
                priceInput.val(cleanValue);
            }
        });
    });
</script>
@endpush