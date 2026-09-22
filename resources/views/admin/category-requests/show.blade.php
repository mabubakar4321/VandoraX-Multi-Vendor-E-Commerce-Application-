@extends('layouts.admin')

@section('title', 'Category Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('admin.category-requests.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                &larr; Back to category requests
            </a>
            <h1 class="mt-3 text-3xl font-bold text-gray-900">{{ $request->category_name }}</h1>
            <p class="mt-1 text-gray-600">Review this category request before taking action.</p>
        </div>
        <span class="inline-block self-start rounded-full px-3 py-1 text-sm font-medium {{ $request->isPending() ? 'bg-yellow-100 text-yellow-800' : ($request->isApproved() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
            {{ ucfirst($request->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="rounded-lg bg-white p-6 shadow lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-900">Request details</h2>
            <dl class="mt-6 space-y-5">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Category name</dt>
                    <dd class="mt-1 text-gray-900">{{ $request->category_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $request->description ?: 'No description provided.' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Requested</dt>
                    <dd class="mt-1 text-gray-900">{{ optional($request->requested_at)->format('M d, Y g:i A') ?: 'Unknown' }}</dd>
                </div>
                @if($request->admin_comment)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Admin comment</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $request->admin_comment }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <aside class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-xl font-semibold text-gray-900">Vendor</h2>
            <dl class="mt-6 space-y-5">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-gray-900">{{ optional($request->vendor->user)->name ?: 'Unknown' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 break-words text-gray-900">{{ optional($request->vendor->user)->email ?: 'Unknown' }}</dd>
                </div>
            </dl>
        </aside>
    </div>

    @if($request->isPending())
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-green-200 bg-green-50 p-6">
                <h2 class="text-xl font-semibold text-green-900">Approve request</h2>
                <p class="mt-1 text-sm text-green-800">Approving creates this category and notifies the vendor.</p>
                <form method="POST" action="{{ route('admin.category-requests.approve', $request) }}" class="mt-5">
                    @csrf
                    <label for="comment" class="block text-sm font-medium text-gray-700">Comment (optional)</label>
                    <textarea id="comment" name="comment" rows="3" maxlength="500" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">{{ old('comment') }}</textarea>
                    <button type="submit" class="mt-4 rounded-lg bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700">
                        Approve request
                    </button>
                </form>
            </section>

            <section class="rounded-lg border border-red-200 bg-red-50 p-6">
                <h2 class="text-xl font-semibold text-red-900">Reject request</h2>
                <p class="mt-1 text-sm text-red-800">Provide a reason so the vendor understands the decision.</p>
                <form method="POST" action="{{ route('admin.category-requests.reject', $request) }}" class="mt-5">
                    @csrf
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="reason" name="reason" rows="3" maxlength="500" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                    <button type="submit" class="mt-4 rounded-lg bg-red-600 px-5 py-2 font-medium text-white hover:bg-red-700">
                        Reject request
                    </button>
                </form>
            </section>
        </div>
    @endif
</div>
@endsection
