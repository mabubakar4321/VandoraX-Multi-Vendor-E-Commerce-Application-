@extends('layouts.admin')

@section('title', 'Category Requests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Category Requests</h1>
        <p class="mt-1 text-gray-600">Review category requests submitted by vendors.</p>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-4">
        <a href="{{ route('admin.category-requests.index', ['status' => 'pending']) }}" class="rounded-lg bg-white p-6 shadow {{ $status === 'pending' ? 'ring-2 ring-indigo-500' : '' }}">
            <p class="text-sm text-gray-600">Pending</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $statistics['pending'] }}</p>
        </a>
        <a href="{{ route('admin.category-requests.index', ['status' => 'approved']) }}" class="rounded-lg bg-white p-6 shadow {{ $status === 'approved' ? 'ring-2 ring-indigo-500' : '' }}">
            <p class="text-sm text-gray-600">Approved</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $statistics['approved'] }}</p>
        </a>
        <a href="{{ route('admin.category-requests.index', ['status' => 'rejected']) }}" class="rounded-lg bg-white p-6 shadow {{ $status === 'rejected' ? 'ring-2 ring-indigo-500' : '' }}">
            <p class="text-sm text-gray-600">Rejected</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $statistics['rejected'] }}</p>
        </a>
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm text-gray-600">Total</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</p>
        </div>
    </div>

    <div class="mb-6 rounded-lg bg-white p-6 shadow">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="search" name="q" value="{{ $query }}" placeholder="Search category, vendor, or email..." class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500 sm:flex-1">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 font-medium text-white hover:bg-indigo-700">Search</button>
            @if($query)
                <a href="{{ route('admin.category-requests.index', ['status' => $status]) }}" class="rounded-lg bg-gray-200 px-6 py-2 text-center font-medium text-gray-900 hover:bg-gray-300">Reset</a>
            @endif
        </form>
    </div>

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $filter => $label)
            <a href="{{ route('admin.category-requests.index', ['status' => $filter, 'q' => $query]) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ $status === $filter ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900 hover:bg-gray-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Vendor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Category</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Requested</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $categoryRequest)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ optional($categoryRequest->vendor->user)->name ?: 'Unknown vendor' }}</p>
                                <p class="text-sm text-gray-600">{{ optional($categoryRequest->vendor->user)->email ?: 'No email' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $categoryRequest->category_name }}</p>
                                @if($categoryRequest->description)
                                    <p class="mt-1 text-sm text-gray-600">{{ Str::limit($categoryRequest->description, 70) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ optional($categoryRequest->requested_at)->format('M d, Y') ?: 'Unknown' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block rounded-full px-3 py-1 text-sm font-medium {{ $categoryRequest->isPending() ? 'bg-yellow-100 text-yellow-800' : ($categoryRequest->isApproved() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($categoryRequest->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.category-requests.show', $categoryRequest) }}" class="font-medium text-indigo-600 hover:text-indigo-900">View details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No category requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($requests, 'links'))
        <div class="mt-6">{{ $requests->appends(['status' => $status, 'q' => $query])->links() }}</div>
    @endif
</div>
@endsection
