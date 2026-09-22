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
                @if ($request->status === 'approved')
                    <span class="bg-forest-100 px-3 py-1.5 text-sm font-semibold text-forest-500">Approved</span>
                @elseif ($request->status === 'rejected')
                    <span class="bg-vermilion-100 px-3 py-1.5 text-sm font-semibold text-vermilion">Rejected</span>
                @else
                    <span class="bg-marigold-100 px-3 py-1.5 text-sm font-semibold text-marigold-600">Pending</span>
                @endif
            </div>

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
