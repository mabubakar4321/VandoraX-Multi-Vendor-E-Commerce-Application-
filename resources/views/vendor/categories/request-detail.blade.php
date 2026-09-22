@extends('layouts.vendor')

@section('title', 'Category Request Details — VandoraX')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('vendor.categories.requests') }}" class="text-sm font-medium text-forest underline decoration-mist underline-offset-4 hover:decoration-forest">Back to category requests</a>

        <div class="mt-6 border border-mist bg-white p-6 sm:p-8" style="border-radius: 2px;">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-mist pb-6">
                <div>
                    <p class="text-sm text-graphite">Category request</p>
                    <h1 class="mt-1 text-[30px] leading-tight">{{ $request->category_name }}</h1>
                </div>
                <div class="flex flex-col gap-2">
                    @if ($request->status === 'approved')
                        <span class="bg-forest-100 px-3 py-1.5 text-sm font-semibold text-forest-500">Approved</span>
                        @if ($request->hasImage())
                            <span class="bg-blue-100 px-3 py-1.5 text-sm font-semibold text-blue-600">✅ Image Uploaded</span>
                        @else
                            <a href="{{ route('vendor.categories.image.edit', $request) }}" class="bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-blue-700 text-center rounded transition">
                                📸 Upload Image
                            </a>
                        @endif
                    @elseif ($request->status === 'rejected')
                        <span class="bg-vermilion-100 px-3 py-1.5 text-sm font-semibold text-vermilion">Rejected</span>
                    @else
                        <span class="bg-marigold-100 px-3 py-1.5 text-sm font-semibold text-marigold-600">Pending</span>
                    @endif
                </div>
            </div>

            <!-- Category Image Section -->
            @if ($request->hasImage())
                <div class="mt-6 border border-mist bg-paper p-6">
                    <h2 class="text-sm font-semibold text-forest mb-4">📸 Category Image</h2>
                    <div class="flex gap-6 flex-col sm:flex-row">
                        <div class="flex-shrink-0">
                            <img
                                src="{{ $request->getImageUrl() }}"
                                alt="{{ $request->image_alt_text }}"
                                class="w-full sm:w-48 h-48 object-cover rounded-lg border border-mist"
                            >
                        </div>
                        <div class="flex-grow">
                            <div class="space-y-3">
                                @if ($request->image_alt_text)
                                    <div>
                                        <p class="text-xs font-semibold text-graphite uppercase">Alt Text</p>
                                        <p class="mt-1 text-sm text-graphite">{{ $request->image_alt_text }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs font-semibold text-graphite uppercase">Status</p>
                                    <p class="mt-1 text-sm text-green-600">✅ Active</p>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('vendor.categories.image.edit', $request) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition">
                                    ✏️ Change Image
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <dl class="mt-6 space-y-5">
                <div>
                    <dt class="text-sm font-semibold text-forest">Reason or description</dt>
                    <dd class="mt-1 whitespace-pre-line text-[15px] leading-relaxed text-graphite">{{ $request->description }}</dd>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-semibold text-forest">Requested</dt>
                        <dd class="mt-1 text-sm text-graphite">{{ optional($request->requested_at)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-forest">Response date</dt>
                        <dd class="mt-1 text-sm text-graphite">{{ optional($request->approved_at)->format('M d, Y') ?? 'Not reviewed yet' }}</dd>
                    </div>
                </div>
                @if ($request->admin_comment)
                    <div class="border-l-[3px] border-forest-500 bg-forest-100 px-4 py-3">
                        <dt class="text-sm font-semibold text-forest">Admin comment</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-graphite">{{ $request->admin_comment }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
@endsection
