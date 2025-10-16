@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-6">
    <div class="bg-white shadow rounded-xl p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Worker Information</h2>
            <div class="flex space-x-2">
                <a href="{{ route('master.worker.edit', $worker) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('master.worker.destroy', $worker) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this worker?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-200 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </form>
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
        <!-- Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Worker Name -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Worker Name</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">{{ $worker->name }}</span>
                </div>
            </div>
            <!-- Worker Code -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Worker Code</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">{{ $worker->code ?: 'Not specified' }}</span>
                </div>
            </div>
            <!-- Category -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Category</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">{{ $worker->category ? $worker->category->name : 'Not specified' }}</span>
                </div>
            </div>
            <!-- Unit -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Unit</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-blue-800">{{ $worker->unit }}</span>
                </div>
            </div>
            <!-- Kualifikasi -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Kualifikasi</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-gray-900 font-medium"> {{ $worker->kualifikasi ?? 'Null' }}</span>
                </div>
            </div>
            <!-- Kewarganegaraan -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Kewarganegaraan</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-gray-900 font-medium"> {{ $worker->Kewarganegaraan ?? 'Null' }}</span>
                </div>
            </div>
            <!-- Price -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Price (Rp)</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Rp {{ number_format($worker->price, 0, ',', '.') }}</span>
                </div>
            </div>
            <!-- TKDN -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">TKDN (%)</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">{{ number_format($worker->tkdn, 1) }}%</span>
                </div>
            </div>
        </div>
        <!-- TKDN Status -->
        <div class="mb-6">
            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">TKDN Status</label>
            @if($worker->tkdn >= 80)
            <div class="flex items-center bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <div class="font-semibold text-green-900">Excellent TKDN Compliance</div>
                    <div class="text-sm text-green-700">This worker meets high TKDN requirements</div>
                </div>
            </div>
            @elseif($worker->tkdn >= 60)
            <div class="flex items-center bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
                <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <div class="font-semibold text-yellow-900">Good TKDN Compliance</div>
                    <div class="text-sm text-yellow-700">This worker meets moderate TKDN requirements</div>
                </div>
            </div>
            @else
            <div class="flex items-center bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <div class="font-semibold text-red-900">Low TKDN Compliance</div>
                    <div class="text-sm text-red-700">This worker has low TKDN percentage</div>
                </div>
            </div>
            @endif
        </div>
        <!-- Timestamps -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Created Date</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-gray-900">{{ $worker->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Last Updated</label>
                <div class="flex items-center bg-gray-50 rounded-lg px-4 py-3 border">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-900">{{ $worker->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 mt-2">
            <a href="{{ route('master.worker.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Workers
            </a>
            <a href="{{ route('master.worker.edit', $worker) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Worker
            </a>
        </div>
    </div>
</div>
@endsection