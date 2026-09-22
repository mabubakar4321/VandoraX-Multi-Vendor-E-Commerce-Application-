@extends('layouts.admin')

@section('title', 'Pending Vendors')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Pending Vendor Applications</h1>
        <p class="text-gray-600 mt-2">Review and approve vendor registrations</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <p class="text-gray-600 text-sm font-medium">Pending Applications</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $vendors->total() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-medium">This Page</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $vendors->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-medium">Under Review</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <p class="text-gray-600 text-sm font-medium">Pending Documents</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">0</p>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($vendors->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Vendor Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Store Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Phone</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Applied</th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($vendors as $vendor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($vendor->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $vendor->user->name }}</p>
                                        <p class="text-xs text-gray-500">ID: {{ $vendor->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $vendor->user->email }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $vendor->store_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($vendor->status === 'pending_documents')
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
                                        📄 Pending Docs
                                    </span>
                                @elseif($vendor->status === 'under_review')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">
                                        🔍 Under Review
                                    </span>
                                @elseif($vendor->status === 'active')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                        ✓ Active
                                    </span>
                                @elseif($vendor->status === 'rejected')
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
                                        ✗ Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $vendor->user->phone_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $vendor->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.vendors.detail', $vendor->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                    <span>Review</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-4 border-t">
                {{ $vendors->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No pending applications</h3>
                <p class="text-gray-600">All vendor applications have been reviewed!</p>
            </div>
        @endif
    </div>
</div>
@endsection