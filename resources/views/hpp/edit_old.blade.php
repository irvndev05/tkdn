@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit HPP</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $hpp->code }}</p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <form action="{{ route('hpp.update', $hpp->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Informasi Umum -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Umum</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="project_id" class="form-label">Pilih Project <span class="text-red-500">*</span></label>
                        <select id="project_id" name="project_id" class="form-select @error('project_id') border-red-500 @enderror" required>
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $hpp->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="form-label">Status HPP <span class="text-red-500">*</span></label>
                        @if(auth()->user()->can('manage-hpp'))
                            <select id="status" name="status" class="form-select @error('status') border-red-500 @enderror" required>
                                <option value="draft" {{ old('status', $hpp->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ old('status', $hpp->status) == 'submitted' ? 'selected' : '' }}>Diajukan</option>
                                <option value="approved" {{ old('status', $hpp->status) == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ old('status', $hpp->status) == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        @else
                            <div class="form-input bg-gray-50 dark:bg-gray-700 cursor-not-allowed">
                                @switch($hpp->status)
                                    @case('draft')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Draft
                                        </span>
                                        @break
                                    @case('submitted')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                            Diajukan
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Disetujui
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <svg class="w-3 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Ditolak
                                        </span>
                                        @break
                                @endswitch
                            </div>
                        @endif
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <div id="project-info" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Informasi Project</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Project</label>
                                    <p id="project-name" class="text-gray-900 dark:text-white">{{ $hpp->project->name ?? '' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Project</label>
                                    <p id="project-type" class="text-gray-900 dark:text-white">
                                        @if($hpp->project)
                                            @if($hpp->project->project_type === 'tkdn_jasa')
                                                TKDN Jasa (Form 3.1 - 3.5)
                                            @elseif($hpp->project->project_type === 'tkdn_barang_jasa')
                                                TKDN Barang & Jasa (Form 4.1 - 4.7)
                                            @else
                                                {{ $hpp->project->project_type }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perusahaan</label>
                                    <p id="project-company" class="text-gray-900 dark:text-white">{{ $hpp->project->company ?? '-' }}</p>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                                    <p id="project-description" class="text-gray-900 dark:text-white">{{ $hpp->project->description ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Pekerjaan -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Item Pekerjaan</h3>
                    <button type="button" onclick="addItem()" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Item
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="items-container" data-count="{{ count($hpp->items) }}" data-ahs='@json($ahsData)' data-projects='@json($projects)'>
                    @foreach($hpp->items as $index => $item)
                    <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white">Item {{ $index + 1 }}</h4>
                            <button type="button" onclick="removeItem(this)" class="btn btn-outline p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="form-label">Uraian Barang/Pekerjaan <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="items[{{ $index }}][description]" value="{{ $item->description }}" class="form-input description-input" required readonly>
                                    <button type="button" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="openAhsModal(this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <input type="hidden" name="items[{{ $index }}][estimation_item_id]" class="estimation-item-id-input" value="{{ $item->estimation_item_id }}">
                                <input type="hidden" name="items[{{ $index }}][ahs_type]" class="ahs-type-input">
                            </div>
                            
                            <!-- Klasifikasi TKDN akan diambil otomatis dari master data -->
                            
                            <div>
                                <label class="form-label">Volume <span class="text-red-500">*</span></label>
                                <input type="number" name="items[{{ $index }}][volume]" value="{{ $item->volume }}" class="form-input volume-input" step="0.01" min="0" required>
                            </div>
                            
                            <div>
                                <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                                <input type="text" name="items[{{ $index }}][unit]" value="{{ $item->unit }}" class="form-input unit-input" required>
                            </div>
                            
                            <div>
                                <label class="form-label">Durasi <span class="text-red-500">*</span></label>
                                <input type="number" name="items[{{ $index }}][duration]" value="{{ $item->duration }}" class="form-input" min="1" required>
                            </div>
                            
                            <div>
                                <label class="form-label">Satuan Durasi <span class="text-red-500">*</span></label>
                                <select name="items[{{ $index }}][duration_unit]" class="form-select" required>
                                    <option value="Hari" {{ $item->duration_unit == 'Hari' ? 'selected' : '' }}>Hari</option>
                                    <option value="Minggu" {{ $item->duration_unit == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                    <option value="Bulan" {{ $item->duration_unit == 'Bulan' ? 'selected' : '' }}>Bulan</option>
                                    <option value="Tahun" {{ $item->duration_unit == 'Tahun' ? 'selected' : '' }}>Tahun</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="form-label">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" class="form-input unit-price-input" step="0.01" min="0" required>
                            </div>
                            
                            <div>
                                <label class="form-label">Jumlah Harga (Rp)</label>
                                <input type="number" name="items[{{ $index }}][total_price]" value="{{ $item->total_price }}" class="form-input total-price-input" step="0.01" readonly>
                            </div>
                        </div>
                        
                        <!-- AHS Detail Information -->
                        @if($item->estimationItem)
                        <div class="ahs-detail-info mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">
                                        Detail AHS
                                    </h5>
                                    <div class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                                        <div class="flex justify-between">
                                            <span class="font-medium">Kode AHS:</span>
                                            <span class="font-mono">{{ $item->estimationItem->estimation->code ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">Judul AHS:</span>
                                            <span>{{ $item->estimationItem->estimation->title ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">Klasifikasi TKDN:</span>
                                            <span>{{ $item->estimationItem->classification_tkdn ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">TKDN Value:</span>
                                            <span>{{ $item->estimationItem->tkdn_value ? $item->estimationItem->tkdn_value . '%' : 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">Kategori:</span>
                                            <span>{{ ucfirst($item->estimationItem->category) ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kalkulasi -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kalkulasi</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="overhead_percentage" class="form-label">Overhead (%) <span class="text-red-500">*</span></label>
                        <input type="number" id="overhead_percentage" name="overhead_percentage" value="{{ old('overhead_percentage', $hpp->overhead_percentage) }}" step="0.01" min="0" max="100" class="form-input @error('overhead_percentage') border-red-500 @enderror" required>
                        @error('overhead_percentage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="margin_percentage" class="form-label">Margin (%) <span class="text-red-500">*</span></label>
                        <input type="number" id="margin_percentage" name="margin_percentage" value="{{ old('margin_percentage', $hpp->margin_percentage) }}" step="0.01" min="0" max="100" class="form-input @error('margin_percentage') border-red-500 @enderror" required>
                        @error('margin_percentage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="ppn_percentage" class="form-label">PPN (%) <span class="text-red-500">*</span></label>
                        <input type="number" id="ppn_percentage" name="ppn_percentage" value="{{ old('ppn_percentage', $hpp->ppn_percentage) }}" step="0.01" min="0" max="100" class="form-input @error('ppn_percentage') border-red-500 @enderror" required>
                        @error('ppn_percentage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea id="notes" name="notes" rows="3" class="form-textarea @error('notes') border-red-500 @enderror">{{ old('notes', $hpp->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('hpp.show', $hpp->id) }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                Update HPP
            </button>
        </div>
    </form>
</div>

<!-- Template for item -->
<template id="item-template">
    <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-medium text-gray-900 dark:text-white">Item <span class="item-number"></span></h4>
            <button type="button" onclick="removeItem(this)" class="btn btn-outline p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Uraian Barang/Pekerjaan <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" name="items[INDEX][description]" class="form-input description-input" required readonly placeholder="Klik untuk pilih data AHS">
                    <button type="button" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="openAhsModal(this)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
                <input type="hidden" name="items[INDEX][estimation_item_id]" class="estimation-item-id-input">
                <input type="hidden" name="items[INDEX][ahs_type]" class="ahs-type-input">
            </div>
            
            <!-- Klasifikasi TKDN akan diambil otomatis dari master data -->
            
            <div>
                <label class="form-label">Volume <span class="text-red-500">*</span></label>
                <input type="number" name="items[INDEX][volume]" class="form-input volume-input" step="0.01" min="0" value="1" required>
            </div>
            
            <div>
                <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="items[INDEX][unit]" class="form-input unit-input" value="Unit" required>
            </div>
            
            <div>
                <label class="form-label">Durasi <span class="text-red-500">*</span></label>
                <input type="number" name="items[INDEX][duration]" class="form-input" min="1" value="1" required>
            </div>
            
            <div>
                <label class="form-label">Satuan Durasi <span class="text-red-500">*</span></label>
                <select name="items[INDEX][duration_unit]" class="form-select" required>
                    <option value="Hari">Hari</option>
                    <option value="Minggu">Minggu</option>
                    <option value="Bulan" selected>Bulan</option>
                    <option value="Tahun">Tahun</option>
                </select>
            </div>
            
            <div>
                <label class="form-label">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="items[INDEX][unit_price]" class="form-input unit-price-input" step="0.01" min="0" value="0" required>
            </div>
            
            <div>
                <label class="form-label">Jumlah Harga (Rp)</label>
                <input type="number" name="items[INDEX][total_price]" class="form-input total-price-input" step="0.01" readonly>
            </div>
        </div>
        
        <!-- AHS Detail Information -->
        <div class="ahs-detail-info hidden mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">
                        Detail AHS
                    </h5>
                    <div class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                        <div class="flex justify-between">
                            <span class="font-medium">Kode AHS:</span>
                            <span class="ahs-code font-mono"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Judul AHS:</span>
                            <span class="ahs-title"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Klasifikasi TKDN:</span>
                            <span class="ahs-classification"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">TKDN Value:</span>
                            <span class="ahs-tkdn-value"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Kategori:</span>
                            <span class="ahs-category"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- AHS Modal - Step 1: Pilih AHS -->
<div id="ahsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pilih AHS</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pilih AHS untuk menambahkan item pekerjaan</p>
                </div>
                <button type="button" onclick="closeAhsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Info Box -->
            <div class="mb-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                AHS Tersedia untuk Proyek
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <p>Menampilkan AHS yang sesuai dengan jenis proyek: <span id="projectTypeInfo" class="font-semibold"></span></p>
                                <p class="mt-1">Klik AHS untuk menambahkan semua item pekerjaan secara otomatis.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <input type="text" id="ahsSearch" placeholder="Cari data AHS..." class="form-input w-full">
            </div>
            
            <div id="ahsList" class="space-y-3 max-h-96 overflow-y-auto">
                <!-- AHS data will be loaded here -->
            </div>
            
        </div>
    </div>
</div>

<script>
const containerEl = document.getElementById('items-container');
let itemIndex = 0;
let ahsData = [];
let projects = [];
let currentProjectType = '';

try {
    projects = JSON.parse(containerEl.dataset.projects || '[]');
} catch (e) {
    projects = [];
}

// Initialize itemIndex based on existing items
function initializeItemIndex() {
    const existingItems = containerEl.querySelectorAll('.item-row');
    itemIndex = existingItems.length;
}

function addItem() {
    const container = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const clone = template.content.cloneNode(true);
    
    // Update index
    const inputs = clone.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.name = input.name.replace('INDEX', itemIndex);
    });
    
    // Update item number
    const itemNumber = clone.querySelector('.item-number');
    itemNumber.textContent = itemIndex + 1;
    
    container.appendChild(clone);
    
    // Add event listeners for calculation
    const volumeInput = container.lastElementChild.querySelector('.volume-input');
    const unitPriceInput = container.lastElementChild.querySelector('.unit-price-input');
    const totalPriceInput = container.lastElementChild.querySelector('.total-price-input');
    
    volumeInput.addEventListener('input', function() { calculateTotal(container.lastElementChild); });
    unitPriceInput.addEventListener('input', function() { calculateTotal(container.lastElementChild); });
    
    itemIndex++;
}

function removeItem(button) {
    const itemRow = button.closest('.item-row');
    itemRow.remove();
}

function calculateTotal(row) {
    const volume = parseFloat(row.querySelector('.volume-input').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
    const totalPrice = volume * unitPrice;
    row.querySelector('.total-price-input').value = totalPrice.toFixed(2);
}

function openAhsModal(button) {
    if (!currentProjectType) {
        alert('Pilih proyek terlebih dahulu');
        return;
    }
    
    document.getElementById('ahsModal').classList.remove('hidden');
    loadAhsData();
}

function closeAhsModal() {
    document.getElementById('ahsModal').classList.add('hidden');
}

// Update project type when project selection changes
function updateProjectType() {
    const projectSelect = document.getElementById('project_id');
    const selectedProject = projects.find(p => p.id == projectSelect.value);
    currentProjectType = selectedProject ? selectedProject.project_type : '';
    
    // Update project type info
    const projectTypeInfo = document.getElementById('projectTypeInfo');
    if (projectTypeInfo) {
        projectTypeInfo.textContent = currentProjectType === 'tkdn_jasa' ? 'TKDN Jasa' : 'TKDN Barang Jasa';
    }
}

function loadAhsData() {
    if (!currentProjectType) {
        alert('Pilih proyek terlebih dahulu');
        return;
    }
    
    const ahsList = document.getElementById('ahsList');
    ahsList.innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div><p class="mt-2 text-gray-500">Memuat data AHS...</p></div>';
    
    // Load AHS data based on project type
    fetch(`/hpp/get-ahs-data-only/${currentProjectType}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            ahsData = data;
            displayAhsData();
        })
        .catch(error => {
            console.error('Error:', error);
            ahsList.innerHTML = '<div class="text-center py-4 text-red-500">Gagal memuat data AHS: ' + error.message + '</div>';
        });
}

function displayAhsData() {
    const ahsList = document.getElementById('ahsList');
    ahsList.innerHTML = '';
    
    if (ahsData.length === 0) {
        ahsList.innerHTML = '<div class="text-center py-8 text-gray-500">Tidak ada AHS yang sesuai dengan jenis proyek ini</div>';
        return;
    }
    
    ahsData.forEach(function(item) {
        const div = document.createElement('div');
        div.className = 'p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';
        div.onclick = function() { selectAhsForItems(item); };
        div.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-medium text-gray-900 dark:text-white text-lg">${item.description}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode: ${item.code}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Jumlah Item: ${item.item_count}</div>
                </div>
                <div class="text-right ml-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">AHS</div>
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Klik untuk tambahkan item</div>
                </div>
            </div>
        `;
        ahsList.appendChild(div);
    });
}

function selectAhsForItems(ahs) {
    // Load AHS items
    fetch(`/hpp/get-ahs-items/${ahs.id}/${currentProjectType}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            // First, try to fill empty rows
            let remainingItems = [...data.items];
            const emptyRows = findEmptyRows();
            
            // Fill empty rows first
            for (let i = 0; i < emptyRows.length && remainingItems.length > 0; i++) {
                const emptyRow = emptyRows[i];
                const item = remainingItems.shift();
                fillExistingRowWithAhsItem(emptyRow, item, ahs);
            }
            
            // Add remaining items as new rows
            if (remainingItems.length > 0) {
                initializeItemIndex();
                remainingItems.forEach(function(item) {
                    addAhsItem(item, ahs);
                });
            }
            
            // Close modal
            closeAhsModal();
            
            // Show success message
            showNotification(`Berhasil menambahkan ${data.items.length} item dari AHS ${ahs.code}`, 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data AHS items');
        });
}

function findEmptyRows() {
    const allRows = containerEl.querySelectorAll('.item-row');
    const emptyRows = [];
    
    allRows.forEach(function(row) {
        const descriptionInput = row.querySelector('.description-input');
        if (descriptionInput && !descriptionInput.value.trim()) {
            emptyRows.push(row);
        }
    });
    
    return emptyRows;
}

function fillExistingRowWithAhsItem(row, item, ahs) {
    // Fill data
    const descriptionInput = row.querySelector('.description-input');
    const estimationItemIdInput = row.querySelector('.estimation-item-id-input');
    const unitPriceInput = row.querySelector('.unit-price-input');
    const unitInput = row.querySelector('.unit-input');
    const volumeInput = row.querySelector('.volume-input');
    
    descriptionInput.value = `${ahs.description} - ${item.description}`;
    estimationItemIdInput.value = item.id;
    unitPriceInput.value = item.unit_price;
    unitInput.value = item.unit || 'Unit';
    volumeInput.value = item.coefficient || 1;
    
    // Show AHS detail information
    const ahsDetailInfo = row.querySelector('.ahs-detail-info');
    const ahsCode = row.querySelector('.ahs-code');
    const ahsTitle = row.querySelector('.ahs-title');
    const ahsClassification = row.querySelector('.ahs-classification');
    const ahsTkdnValue = row.querySelector('.ahs-tkdn-value');
    const ahsCategory = row.querySelector('.ahs-category');
    
    if (ahsDetailInfo) {
        ahsCode.textContent = ahs.code;
        ahsTitle.textContent = ahs.title;
        ahsClassification.textContent = item.classification_tkdn || 'N/A';
        ahsTkdnValue.textContent = item.tkdn_value ? item.tkdn_value + '%' : 'N/A';
        ahsCategory.textContent = item.category || 'N/A';
        
        // Show the detail info
        ahsDetailInfo.classList.remove('hidden');
    }
    
    // Calculate total
    calculateTotal(row);
}

function addAhsItem(item, ahs) {
    const container = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const clone = template.content.cloneNode(true);
    
    // Update index
    const inputs = clone.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.name = input.name.replace('INDEX', itemIndex);
    });
    
    // Update item number
    const itemNumber = clone.querySelector('.item-number');
    itemNumber.textContent = itemIndex + 1;
    
    // Fill data
    const descriptionInput = clone.querySelector('.description-input');
    const estimationItemIdInput = clone.querySelector('.estimation-item-id-input');
    const unitPriceInput = clone.querySelector('.unit-price-input');
    const unitInput = clone.querySelector('.unit-input');
    const volumeInput = clone.querySelector('.volume-input');
    
    descriptionInput.value = `${ahs.description} - ${item.description}`;
    estimationItemIdInput.value = item.id;
    unitPriceInput.value = item.unit_price;
    unitInput.value = item.unit || 'Unit';
    volumeInput.value = item.coefficient || 1;
    
    // Show AHS detail information
    const ahsDetailInfo = clone.querySelector('.ahs-detail-info');
    const ahsCode = clone.querySelector('.ahs-code');
    const ahsTitle = clone.querySelector('.ahs-title');
    const ahsClassification = clone.querySelector('.ahs-classification');
    const ahsTkdnValue = clone.querySelector('.ahs-tkdn-value');
    const ahsCategory = clone.querySelector('.ahs-category');
    
    ahsCode.textContent = ahs.code;
    ahsTitle.textContent = ahs.title;
    ahsClassification.textContent = item.classification_tkdn || 'N/A';
    ahsTkdnValue.textContent = item.tkdn_value ? item.tkdn_value + '%' : 'N/A';
    ahsCategory.textContent = item.category || 'N/A';
    
    // Show the detail info
    ahsDetailInfo.classList.remove('hidden');
    
    container.appendChild(clone);
    
    // Add event listeners for calculation
    const volumeInputEl = container.lastElementChild.querySelector('.volume-input');
    const unitPriceInputEl = container.lastElementChild.querySelector('.unit-price-input');
    const totalPriceInputEl = container.lastElementChild.querySelector('.total-price-input');
    
    volumeInputEl.addEventListener('input', function() { calculateTotal(container.lastElementChild); });
    unitPriceInputEl.addEventListener('input', function() { calculateTotal(container.lastElementChild); });
    
    // Calculate initial total
    calculateTotal(container.lastElementChild);
    
    itemIndex++;
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ'}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add event listener for project selection
document.addEventListener('DOMContentLoaded', function() {
    // Initialize itemIndex based on existing items
    initializeItemIndex();
    
    const projectSelect = document.getElementById('project_id');
    if (projectSelect) {
        projectSelect.addEventListener('change', function() {
            updateProjectType();
        });
        
        // Initialize project type on page load
        updateProjectType();
    }
});


function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Project selection handler
document.getElementById('project_id').addEventListener('change', function() {
    const projectId = this.value;
    const projectInfo = document.getElementById('project-info');
    
    if (projectId) {
        const project = projects.find(function(p) { return p.id === projectId; });
        if (project) {
            document.getElementById('project-name').textContent = project.name;
            document.getElementById('project-type').textContent = getProjectTypeLabel(project.project_type);
            document.getElementById('project-company').textContent = project.company || '-';
            document.getElementById('project-description').textContent = project.description || '-';
        }
    }
});

// Helper function to get project type label
function getProjectTypeLabel(projectType) {
    switch(projectType) {
        case 'tkdn_jasa':
            return 'TKDN Jasa (Form 3.1 - 3.5)';
        case 'tkdn_barang_jasa':
            return 'TKDN Barang & Jasa (Form 4.1 - 4.7)';
        default:
            return projectType || '-';
    }
}

// Search functionality
document.getElementById('ahsSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const ahsItems = document.querySelectorAll('#ahsList > div');
    
    ahsItems.forEach(function(item) {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Close modal when clicking outside
document.getElementById('ahsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAhsModal();
    }
});

// Add event listeners to existing rows
document.querySelectorAll('#items-container .item-row').forEach(function(row) {
    const volumeInput = row.querySelector('.volume-input');
    const unitPriceInput = row.querySelector('.unit-price-input');
    
    if (volumeInput && unitPriceInput) {
        volumeInput.addEventListener('input', function() { calculateTotal(row); });
        unitPriceInput.addEventListener('input', function() { calculateTotal(row); });
    }
});
</script>
@endsection
