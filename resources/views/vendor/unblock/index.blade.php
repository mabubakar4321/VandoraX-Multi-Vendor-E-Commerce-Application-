@extends('layouts.vendor')

@section('title', 'Unblock Requests')

@section('content')
<div class="w-full px-4 sm:px-6 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Unblock Requests</h1>
            <p class="text-sm text-gray-500 mt-1">View and manage your unblock request history.</p>
        </div>
        <a href="{{ route('vendor.unblock.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Request
        </a>
    </div>

    {{-- Mobile cards (sm-) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sm:hidden divide-y divide-gray-100 mb-8">
        @forelse ($requests as $request)
            <div class="p-4">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</span>
                    @php
                        $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($request->status) }}</span>
                </div>
                @if ($request->admin_response)
                    <p class="text-xs text-gray-500 mt-2">{{ Str::limit($request->admin_response, 100) }}</p>
                @endif
                <a href="{{ route('vendor.unblock.show', $request) }}" class="inline-block mt-3 text-xs font-medium text-emerald-600">View details →</a>
            </div>
        @empty
            <div class="px-4 py-10 text-center text-gray-500 text-sm">No unblock requests yet.</div>
        @endforelse
    </div>

    {{-- Desktop table (sm+) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sm:block hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($request->status) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->reviewed_at ? $request->reviewed_at->format('M d, Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('vendor.unblock.show', $request) }}" class="text-emerald-600 hover:text-emerald-900 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No unblock requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($requests instanceof \Illuminate\Pagination\LengthAwarePaginator && $requests->hasPages())
        <div class="flex justify-center sm:justify-end">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
