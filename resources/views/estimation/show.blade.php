@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
<div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('master.estimation.index') }}" class="btn btn-outline p-2 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">AHS Details</h1>
                <p class="text-gray-600 dark:text-gray-400">View unit price analysis information</p>
</div>
        </div>
    </div>

    <!-- Estimation Details -->
    <div class="w-full">
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">AHS Information</h3>
                    <div class="flex space-x-2">
                        <a href="{{ route('master.estimation.edit', $estimation->id) }}" class="btn btn-outline btn-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Left: Info List -->
                    <div class="space-y-6">
            <div>
                            <!-- <div class="text-xs text-gray-500 uppercase mb-1">AHS Code</div>
                            <div class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4M7 7h.01M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" /></svg>
                                {{ $estimation->code ?? '-' }}
                            </div> -->
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase mb-1">Title</div>
                            <div class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                {{ $estimation->title }}
                            </div>
            </div>
            <div>
                            <div class="text-xs text-gray-500 uppercase mb-1">Created</div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ $estimation->created_at->format('d M Y, H:i') }}
                            </div>
            </div>
            <div>
                            <div class="text-xs text-gray-500 uppercase mb-1">Updated</div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $estimation->updated_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                    <!-- Right: Stats -->
                    <div class="flex flex-col justify-center items-center gap-6">
                        <div>
                            <div class="text-xs text-gray-500 uppercase mb-1 text-center">Total</div>
                            <div class="font-extrabold text-2xl text-gray-900 dark:text-white text-center">Rp {{ number_format($estimation->total, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase mb-1 text-center">Final Price</div>
                            <div class="font-extrabold text-2xl text-green-600 text-center">Rp {{ number_format($estimation->total_unit_price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-6 mt-6">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Created: {{ $estimation->created_at->format('d M Y, H:i') }}
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Updated: {{ $estimation->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('master.estimation.index') }}" class="btn btn-outline flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Estimations
                    </a>
                    <a href="{{ route('master.estimation.edit', $estimation->id) }}" class="btn btn-primary flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Estimation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL ITEM -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow mb-8 mt-8">
        <div class="flex items-center justify-between px-6 pt-6 pb-2">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                AHS Items
            </h3>
            <span class="badge badge-primary">{{ $estimation->items->count() }} items</span>
        </div>
        <div class="overflow-x-auto px-6 pb-6">
            <table class="table table-modern min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Nama/Peralatan</th>
                        <th>Satuan</th>
                        <th>Koefisien</th>
                        <th>Harga</th>
                        <th>Jumlah Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($estimation->items as $i => $item)
                        @php
                            // Get the correct name and unit based on category and reference
                            $itemName = $item->equipment_name;
                            $itemUnit = '';
                            
                            if ($item->category === 'worker' && $item->worker) {
                                $itemName = $item->worker->name;
                                $itemUnit = $item->worker->unit;
                            } elseif ($item->category === 'material' && $item->material) {
                                $itemName = $item->material->name . ($item->material->specification ? ' - ' . $item->material->specification : '');
                                $itemUnit = $item->material->unit;
                            } elseif ($item->category === 'equipment' && $item->equipment) {
                                $itemName = $item->equipment->name . ($item->equipment->description ? ' - ' . $item->equipment->description : '');
                                $itemUnit = 'jam';
                            }
                        @endphp
                        <tr>
                        <td>{{ $i+1 }}</td>
                        <td>
                            <span class="badge
                                @if($item->category === 'worker') badge-blue
                                @elseif($item->category === 'material') badge-green
                                @else badge-gray @endif
                            ">
                                @if($item->category === 'worker')
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @elseif($item->category === 'material')
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="18" height="18" rx="4" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6v6H9z" />
                                    </svg>
                                @endif
                                {{ ucfirst($item->category) }}
                            </span>
                        </td>
                        <td>{{ $itemName }}</td>
                        <td>{{ $itemUnit }}</td>
                        <td>{{ number_format($item->coefficient, 5, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">Belum ada item AHS</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- STATISTIK KATEGORI -->
    @if($estimation->items->count() > 0)
    @php
        $categoryStats = $estimation->items->groupBy('category')->map(function($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('total_price')
            ];
        });
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Statistik Kategori
        </h3>
        <div class="flex flex-col md:flex-row gap-6">
            @foreach($categoryStats as $category => $stats)
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-3 h-3 rounded-full {{ $category === 'worker' ? 'bg-blue-500' : ($category === 'material' ? 'bg-green-500' : 'bg-gray-500') }}"></span>
                    <span class="capitalize text-sm">{{ $category }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-1">
                    <div class="h-3 rounded-full {{ $category === 'worker' ? 'bg-blue-500' : ($category === 'material' ? 'bg-green-500' : 'bg-gray-500') }}" style="width: {{ $estimation->total_unit_price > 0 ? ($stats['total'] / $estimation->total_unit_price) * 100 : 0 }}%"></div>
                </div>
                <div class="text-xs text-gray-500">{{ $stats['count'] }} items, Rp {{ number_format($stats['total'], 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection 