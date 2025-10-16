@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Generate Service TKDN</h1>
            <p class="text-gray-600 dark:text-gray-400">Buat formulir TKDN jasa otomatis dari data HPP yang dipilih</p>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <form action="{{ route('service.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Pilihan Data Sumber -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pilihan Data Sumber</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="project_id" class="form-label">Pilih Proyek <span class="text-red-500">*</span></label>
                        <select name="project_id" id="project_id" class="form-select @error('project_id') border-red-500 @enderror" required onchange="loadHppData()">
                            <option value="">Pilih Proyek</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Kategori Form TKDN</label>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Otomatis Ditentukan</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-300">Kategori form akan ditentukan otomatis berdasarkan jenis form yang tersedia di HPP yang dipilih</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="hpp-selection" class="hidden">
                        <label for="hpp_id" class="form-label">Pilih HPP <span class="text-red-500">*</span></label>
                        <select name="hpp_id" id="hpp_id" class="form-select @error('hpp_id') border-red-500 @enderror" required>
                            <option value="">Pilih HPP</option>
                        </select>
                        @error('hpp_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Data yang Akan Di-generate -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview Data Service yang Akan Di-generate</h3>
            </div>
            <div class="card-body">
                <div id="hpp-preview" class="hidden">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                        <h4 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-3">Data HPP Sumber</h4>
                        <div id="hpp-details" class="space-y-2">
                            <!-- HPP details will be loaded here -->
                        </div>
                    </div>

                    <!-- Preview Service yang akan dibuat -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-green-800 dark:text-green-200 mb-3">Service yang Akan Dibuat</h4>
                        <div id="service-preview" class="space-y-2">
                            <!-- Service preview will be loaded here -->
                        </div>
                    </div>
                </div>

                <div id="no-hpp-message" class="hidden">
                    <div class="text-center py-8">
                        <div class="text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">Tidak ada HPP ditemukan</p>
                            <p class="text-sm">Buat HPP terlebih dahulu untuk project ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('service.index') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                🚀 Generate Service TKDN Otomatis
            </button>
        </div>
    </form>
</div>

<script>
    function loadHppData() {
        const projectId = document.getElementById('project_id').value;
        const hppSelection = document.getElementById('hpp-selection');
        const noHppMessage = document.getElementById('no-hpp-message');
        const submitBtn = document.getElementById('submit-btn');

        if (!projectId) {
            hppSelection.classList.add('hidden');
            noHppMessage.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }

        // Show loading
        hppSelection.classList.remove('hidden');
        noHppMessage.classList.add('hidden');

        // Fetch HPP data
        fetch(`/service/get-hpp-data?project_id=${projectId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    populateHppOptions(data.data);
                    hppSelection.classList.remove('hidden');
                    noHppMessage.classList.add('hidden');
                } else {
                    hppSelection.classList.add('hidden');
                    noHppMessage.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                hppSelection.classList.add('hidden');
                noHppMessage.classList.remove('hidden');
            });
    }

    function populateHppOptions(hppData) {
        const hppSelect = document.getElementById('hpp_id');
        const hppPreview = document.getElementById('hpp-preview');
        const hppDetails = document.getElementById('hpp-details');

        // Clear existing options
        hppSelect.innerHTML = '<option value="">Pilih HPP</option>';

        // Add HPP options
        hppData.forEach(hpp => {
            const option = document.createElement('option');
            option.value = hpp.id;
            option.textContent = `${hpp.name_hpp} - Total: Rp ${formatNumber(hpp.total_cost)} (${hpp.items_count} items)`;
            option.dataset.hppData = JSON.stringify(hpp);
            hppSelect.appendChild(option);

        });

        // Show preview when HPP is selected
        hppSelect.addEventListener('change', function() {
            const selectedHpp = this.value;
            if (selectedHpp) {
                const hppData = JSON.parse(this.selectedOptions[0].dataset.hppData);
                showHppPreview(hppData);
                document.getElementById('submit-btn').disabled = false;
            } else {
                hppPreview.classList.add('hidden');
                document.getElementById('submit-btn').disabled = true;
            }
        });
    }

    function showHppPreview(hppData) {
        const hppPreview = document.getElementById('hpp-preview');
        const hppDetails = document.getElementById('hpp-details');
        const servicePreview = document.getElementById('service-preview');

        // HPP Details
        let hppDetailsHtml = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="font-medium">Kode HPP:</span> ${hppData.code}
                 <span class="font-medium">Nama:</span> ${hppData.name_hpp}
            </div>
            <div>
                <span class="font-medium">Total Biaya:</span> Rp ${formatNumber(hppData.total_cost)}
            </div>
            <div>
                <span class="font-medium">Jumlah Items:</span> ${hppData.items_count}
            </div>
            <div>
                <span class="font-medium">Nama Proyek:</span> ${hppData.project_name}
            </div>
        </div>
        
        <div class="mt-4">
            <h5 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Breakdown TKDN (${hppData.project_type === 'tkdn_jasa' ? 'Form 3.1-3.5' : 'Form 4.1-4.7'}):</h5>
            <div class="space-y-2">
    `;

        if (Object.keys(hppData.tkdn_breakdown).length === 0) {
            hppDetailsHtml += `
            <div class="text-sm text-gray-500 dark:text-gray-400 italic">
                Tidak ada items HPP yang sesuai dengan jenis project ini
            </div>
        `;
        } else {
            Object.entries(hppData.tkdn_breakdown).forEach(([classification, data]) => {
                hppDetailsHtml += `
                <div class="flex justify-between text-sm">
                    <span>Form ${classification}:</span>
                    <span>${data.count} items - Rp ${formatNumber(data.total_cost)}</span>
                </div>
            `;
            });
        }

        hppDetailsHtml += `
            </div>
        </div>
    `;

        // Service Preview - service type akan ditentukan otomatis berdasarkan project type
        let servicePreviewHtml = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="font-medium">Nama Service:</span> Service TKDN - ${hppData.nama_hpp}
            </div>
            <div>
                <span class="font-medium">Jenis Project:</span> ${hppData.project_type === 'tkdn_jasa' ? 'TKDN Jasa' : 'TKDN Barang & Jasa'}
            </div>
            <div>
                <span class="font-medium">Penyedia:</span> ${hppData.project_name}
            </div>
            <div>
                <span class="font-medium">Dokumen:</span> DOC-${hppData.code}
            </div>
        </div>
        
        <div class="mt-4">
            <h5 class="font-medium text-green-800 dark:text-green-200 mb-2">Yang Akan Di-generate:</h5>
            <div class="space-y-2 text-sm">
                <div>✅ Service dengan nama otomatis</div>
                <div>✅ Provider dan user dari data proyek</div>
                <div>✅ Dokumen number otomatis</div>
                <div>✅ Form TKDN sesuai jenis project: ${getTkdnFormDescription(hppData.project_type)}</div>
                <div>✅ Service items dari HPP items yang sesuai (${Object.keys(hppData.tkdn_breakdown).length} form tersedia)</div>
                ${Object.keys(hppData.tkdn_breakdown).length === 0 ? '<div class="text-yellow-600 dark:text-yellow-400">⚠️ Tidak ada HPP items yang sesuai dengan jenis project ini</div>' : ''}
            </div>
        </div>
    `;

        hppDetails.innerHTML = hppDetailsHtml;
        servicePreview.innerHTML = servicePreviewHtml;
        hppPreview.classList.remove('hidden');
    }


    function getTkdnFormDescription(projectType) {
        const descriptions = {
            'tkdn_jasa': 'Form 3.1 - 3.5 (TKDN Jasa)',
            'tkdn_barang_jasa': 'Form 4.1 - 4.7 (TKDN Barang & Jasa)'
        };

        return descriptions[projectType] || 'Form TKDN sesuai jenis project';
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const hppId = document.getElementById('hpp_id').value;
            if (!hppId) {
                e.preventDefault();
                alert('Silakan pilih HPP terlebih dahulu');
                return false;
            }

            // Konfirmasi sebelum generate
            if (!confirm('Apakah Anda yakin ingin generate Service TKDN dari data HPP ini? Semua data akan dibuat otomatis.')) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endsection