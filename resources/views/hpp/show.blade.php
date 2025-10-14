@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Detail HPP</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $hpp->code }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-2">
            @if($hpp->status === 'draft')
            <a href="{{ route('hpp.edit', $hpp->id) }}" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Status Badge -->
    <div class="flex items-center space-x-2">
        @php
        $statusClasses = [
        'draft' => 'badge-warning',
        'submitted' => 'badge-primary',
        'approved' => 'badge-success',
        'rejected' => 'badge-danger',
        ];
        $statusLabels = [
        'draft' => 'Draft',
        'submitted' => 'Diajukan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        ];
        @endphp
        <span class="badge {{ $statusClasses[$hpp->status] }}">
            {{ $statusLabels[$hpp->status] }}
        </span>
        <span class="badge bg-green-100 text-green-800">
            HPP
        </span>
    </div>

    <!-- Informasi Umum -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Umum</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Project</label>
                    <p class="text-gray-900 dark:text-white">{{ $hpp->project->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Project</label>
                    <p class="text-gray-900 dark:text-white">
                        @if($hpp->project)
                        @if($hpp->project->project_type === 'tkdn_jasa')
                        TKDN Jasa (Form 3.1 - 3.5)
                        @elseif($hpp->project->project_type === 'tkdn_barang_jasa')
                        TKDN Barang & Jasa (Form 4.1 - 4.7)
                        @else
                        {{ $hpp->project->project_type }}
                        @endif
                        @else
                        N/A
                        @endif
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perusahaan</label>
                    <p class="text-gray-900 dark:text-white">{{ $hpp->project->company ?? 'N/A' }}</p>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Project</label>
                    <p class="text-gray-900 dark:text-white">{{ $hpp->project->description ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi</label>
                    <p class="text-gray-900 dark:text-white">{{ $hpp->project->location ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Project</label>
                    <p class="text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $hpp->project->status ?? 'N/A')) }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                    <p class="text-gray-900 dark:text-white">{{ $hpp->project->start_date ? \Carbon\Carbon::parse($hpp->project->start_date)->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-6 px-4 py-2" aria-label="Tabs">
            <button
                onclick="showTab('data-tab')"
                id="data-tab-btn"
                class="tab-button group relative min-w-0 flex-1 overflow-hidden bg-white dark:bg-gray-900 py-3 px-4 text-center text-sm font-medium transition-colors duration-150 ease-in-out hover:text-gray-700 dark:hover:text-gray-300 focus:z-10"
            >
                <span class="active-indicator absolute inset-x-0 bottom-0 h-0.5 bg-blue-500 dark:bg-blue-400 opacity-0 transition-opacity duration-200"></span>
                    Data HPP
            </button>
            <button
                onclick="showTab('log-tab')"
                id="log-tab-btn"
                class="tab-button group relative min-w-0 flex-1 overflow-hidden bg-white dark:bg-gray-900 py-3 px-4 text-center text-sm font-medium transition-colors duration-150 ease-in-out hover:text-gray-700 dark:hover:text-gray-300 focus:z-10"
            >
                <span class="active-indicator absolute inset-x-0 bottom-0 h-0.5 bg-blue-500 dark:bg-blue-400 opacity-0 transition-opacity duration-200"></span>
                    Log Activity HPP
            </button>
        </nav>
    </div>

    <style>
        .tab-button.active .active-indicator {
            opacity: 1;
        }
    </style>
    <!-- Tab Content -->
    <!-- Data Tab -->
    <div id="data-tab" class="tab-content">
        <!-- Tabel Detail Item -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Item Pekerjaan</h3>
            </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>URAIAN BARANG/PEKERJAAN</th>
                            <th>Klasifikasi TKDN</th>
                            <th>VOLUME</th>
                            <th>SAT.</th>
                            <th>Durasi</th>
                            <th>Sat</th>
                            <th>HAR SAT.</th>
                            <th>JUMLAH HARGA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hpp->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="text-center font-medium">{{ $item->item_number }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">{{ $item->tkdn_classification }}</td>
                            <td class="text-right">{{ number_format($item->volume, 2) }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-center">{{ $item->duration }}</td>
                            <td class="text-center">{{ $item->duration_unit }}</td>
                            <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-right font-medium">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach

                        <!-- Sub Total HPP -->
                        <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                            <td colspan="7" class="text-right">SUB TOTAL HPP</td>
                            <td colspan="2" class="text-right">Rp {{ number_format($hpp->sub_total_hpp, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Overhead -->
                        <tr>
                            <td class="text-center font-medium">VI</td>
                            <td colspan="6">Overhead</td>
                            <td class="text-center">{{ $hpp->overhead_percentage }}%</td>
                            <td class="text-right font-medium">Rp {{ number_format($hpp->overhead_amount, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Margin -->
                        <tr>
                            <td class="text-center font-medium">VII</td>
                            <td colspan="6">Margin</td>
                            <td class="text-center">{{ $hpp->margin_percentage }}%</td>
                            <td class="text-right font-medium">Rp {{ number_format($hpp->margin_amount, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Sub Total -->
                        <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                            <td colspan="7" class="text-right">SUB TOTAL</td>
                            <td colspan="2" class="text-right">Rp {{ number_format($hpp->sub_total, 0, ',', '.') }}</td>
                        </tr>

                        <!-- PPN -->
                        <tr>
                            <td colspan="7" class="font-semibold text-right">PPN</td>
                            <td class="text-center">{{ $hpp->ppn_percentage }}%</td>
                            <td class="text-right font-medium">Rp {{ number_format($hpp->ppn_amount, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Grand Total -->
                        <tr class="bg-primary-50 dark:bg-primary-900">
                            <td colspan="7" class="font-bold text-right text-primary-600 dark:text-primary-400">GRAND TOTAL</td>
                            <td colspan="2" class="text-right font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($hpp->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ringkasan HPP -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan HPP</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($hpp->sub_total_hpp, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Sub Total HPP</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($hpp->sub_total, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Sub Total (Setelah Overhead & Margin)</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($hpp->grand_total, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Grand Total (Setelah PPN)</div>
                </div>
            </div>
        </div>
    </div>

        @if($hpp->notes)
        <!-- Catatan -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Catatan</h3>
            </div>
            <div class="card-body">
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                    <p class="text-yellow-700 dark:text-yellow-300">{{ $hpp->notes }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Log Activity Tab -->
    <div id="log-tab" class="tab-content hidden">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Log Activity HPP</h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Log creation --}}
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td>{{ $hpp->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $hpp->creator->name ?? 'System' }}</td>
                                <td>
                                    @if($hpp->creator)
                                        <span class="badge bg-blue-100 text-blue-800">
                                            {{ $hpp->creator->roles->first()->name ?? 'User' }}
                                        </span>
                                    @else
                                        <span class="badge bg-gray-100 text-gray-800">System</span>
                                    @endif
                                </td>
                                <td>HPP dibuat dengan kode: {{ $hpp->code }}</td>
                                <td><span class="badge badge-primary">Created</span></td>
                            </tr>

                            {{-- Log updates --}}
                            @if($hpp->updated_at != $hpp->created_at)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td>{{ $hpp->updated_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $hpp->updater->name ?? 'System' }}</td>
                                <td>
                                    @if($hpp->updater)
                                        <span class="badge bg-blue-100 text-blue-800">
                                            {{ $hpp->updater->roles->first()->name ?? 'User' }}
                                        </span>
                                    @else
                                        <span class="badge bg-gray-100 text-gray-800">System</span>
                                    @endif
                                </td>
                                <td>HPP diperbarui</td>
                                <td><span class="badge badge-warning">Updated</span></td>
                            </tr>
                            @endif

                            {{-- Log status changes --}}
                            @if($hpp->status === 'submitted')
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td>{{ $hpp->submitted_at ? \Carbon\Carbon::parse($hpp->submitted_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $hpp->submitter->name ?? '-' }}</td>
                                <td>
                                    @if($hpp->submitter)
                                        <span class="badge bg-blue-100 text-blue-800">
                                            {{ $hpp->submitter->roles->first()->name ?? 'User' }}
                                        </span>
                                    @endif
                                </td>
                                <td>HPP diajukan untuk persetujuan</td>
                                <td><span class="badge badge-primary">Submitted</span></td>
                            </tr>
                            @endif

                            @if($hpp->status === 'approved')
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td>{{ $hpp->approved_at ? \Carbon\Carbon::parse($hpp->approved_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $hpp->approver->name ?? '-' }}</td>
                                <td>
                                    @if($hpp->approver)
                                        <span class="badge bg-green-100 text-green-800">
                                            {{ $hpp->approver->roles->first()->name ?? 'Approver' }}
                                        </span>
                                    @endif
                                </td>
                                <td>HPP disetujui{{ $hpp->approval_notes ? ': ' . $hpp->approval_notes : '' }}</td>
                                <td><span class="badge badge-success">Approved</span></td>
                            </tr>
                            @endif

                            @if($hpp->status === 'rejected')
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td>{{ $hpp->rejected_at ? \Carbon\Carbon::parse($hpp->rejected_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $hpp->rejector->name ?? '-' }}</td>
                                <td>
                                    @if($hpp->rejector)
                                        <span class="badge bg-red-100 text-red-800">
                                            {{ $hpp->rejector->roles->first()->name ?? 'Approver' }}
                                        </span>
                                    @endif
                                </td>
                                <td>HPP ditolak{{ $hpp->rejection_notes ? ': ' . $hpp->rejection_notes : '' }}</td>
                                <td><span class="badge badge-danger">Rejected</span></td>
                            </tr>
                            @endif

                            {{-- If no activity logs exist --}}
                            @if($hpp->status === 'draft' && $hpp->updated_at == $hpp->created_at)
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    Belum ada activity log selain pembuatan HPP
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-6">
        <a href="{{ route('hpp.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
        </a>
    </div>
</div>

<style>
.tab-button {
    @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300;
}

.tab-button.active {
    @apply border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-500;
}

.tab-content {
    @apply space-y-6;
}

.tab-content.hidden {
    @apply hidden;
}
</style>

<script>
function showTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(tabId).classList.remove('hidden');
    
    // Add active class to selected tab button
    document.getElementById(tabId + '-btn').classList.add('active');
}

// Initialize tabs
document.addEventListener('DOMContentLoaded', function() {
    showTab('data-tab');
});
</script>

@endsection