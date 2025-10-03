@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">AHS</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola data Analisa Harga Satuan pekerjaan</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Delete All Button -->
                <button type="button" onclick="openDeleteAllModal()" class="btn btn-error flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete All
                </button>
                <!-- Add AHS Button -->
                <a href="{{ route('master.estimation.create') }}" class="btn btn-primary flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add AHS
                </a>
            </div>
        </div>
    </div>
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
    
    <!-- AHS Table -->
    <div class="card">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar AHS</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Click row to view details
                </p>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <!-- <th>Kode</th> -->
                            <th>Judul</th>
                            <th>Total</th>
                            <th>Margin</th>
                            <th>Harga Satuan</th>
                            <th class="text-center">Jumlah Item</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($estimations as $i => $est)
                        <tr class="cursor-pointer hover:bg-blue-100 dark:hover:bg-gray-600 transition-colors border-b border-gray-100 dark:border-gray-700" data-detail-url="{{ route('master.estimation.show', $est->id) }}" onclick="goToDetail(this, event)">
                            <td>{{ $i+1 }}</td>
                            <!-- <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <rect x="3" y="3" width="18" height="18" rx="4" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6v6H9z" />
                                        </svg>
                                    </div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $est->code ?? '-' }}</div>
                                </div>
                            </td> -->
                            <td>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $est->title }}</span>
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($est->total,0,',','.') }}</div>
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-white">{{ number_format($est->margin,0,',','.') }} %</div>
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($est->total_unit_price,0,',','.') }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $est->items_count }}</span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center space-x-2" onclick="event.stopPropagation()">
                                    <a href="{{ route('master.estimation.edit', $est->id) }}" class="btn btn-outline p-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('master.estimation.destroy', $est->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus AHS ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada AHS ditemukan</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan membuat AHS pertama Anda</p>
                                    <a href="{{ route('master.estimation.create') }}" class="btn btn-primary">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Tambah AHS
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($estimations->hasPages())
            {{ $estimations->links('components.pagination') }}
        @endif
    </div>
</div>

<!-- Delete All Confirmation Modal -->
<div id="deleteAllModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-red-600 dark:text-red-400">Delete All Estimations (AHS)</h3>
                <button onclick="closeDeleteAllModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="flex items-center mb-3">
                    <svg class="w-8 h-8 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">Are you sure?</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">This action cannot be undone.</p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    This will permanently delete <strong>ALL</strong> estimation (AHS) records from the database. 
                    All data will be lost and cannot be recovered.
                </p>
            </div>

            <form action="{{ route('master.estimation.delete-all') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-error flex-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Yes, Delete All
                    </button>
                    <button type="button" onclick="closeDeleteAllModal()" class="btn btn-outline flex-1">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function goToDetail(element, event) {
    // Check if the click is on a button or link
    if (event.target.closest('button') || event.target.closest('a')) {
        return;
    }
    
    const detailUrl = element.getAttribute('data-detail-url');
    if (detailUrl) {
        window.location.href = detailUrl;
    }
}

function openDeleteAllModal() {
    document.getElementById('deleteAllModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteAllModal() {
    document.getElementById('deleteAllModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('deleteAllModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteAllModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteAllModal();
    }
});
</script>
@endsection 