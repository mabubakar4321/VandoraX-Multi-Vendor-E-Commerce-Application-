@extends('layouts.vendor')

@section('title', 'My Category Requests — VandoraX')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-forest-500">Seller Centre</p>
            <h1 class="mt-1 text-[32px] leading-tight">Category requests</h1>
            <p class="mt-2 text-[15px] text-graphite">Track categories you have requested for your products.</p>
        </div>
        <a href="{{ route('vendor.categories.request.create') }}" class="btn-primary w-auto px-5">
            Request a category
        </a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['Pending', $pending, 'bg-marigold-100 text-marigold-600'],
            ['Approved', $requests->where('status', 'approved')->count(), 'bg-forest-100 text-forest-500'],
            ['Rejected', $requests->where('status', 'rejected')->count(), 'bg-vermilion-100 text-vermilion'],
        ] as [$label, $count, $style])
            <div class="border border-mist bg-white p-5" style="border-radius: 2px;">
                <p class="text-sm text-graphite">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl text-forest">{{ $count }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left">
                <thead class="border-b border-mist bg-paper">
                    <tr>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Category</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Status</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Requested</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Response</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @forelse ($requests as $request)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-medium text-forest">{{ $request->category_name }}</p>
                                @if ($request->description)
                                    <p class="mt-1 text-sm text-graphite/70">{{ Str::limit($request->description, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($request->status === 'approved')
                                    <span class="bg-forest-100 px-2.5 py-1 text-xs font-semibold text-forest-500">Approved</span>
                                @elseif ($request->status === 'rejected')
                                    <span class="bg-vermilion-100 px-2.5 py-1 text-xs font-semibold text-vermilion">Rejected</span>
                                @else
                                    <span class="bg-marigold-100 px-2.5 py-1 text-xs font-semibold text-marigold-600">Pending</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-graphite">{{ optional($request->requested_at)->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-sm text-graphite">{{ optional($request->approved_at)->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('vendor.categories.request.show', $request) }}" class="text-sm font-semibold text-forest underline decoration-mist underline-offset-4 hover:decoration-forest">View</a>
                                    @if ($request->isApproved())
                                        <a href="{{ route('vendor.categories.image.edit', $request) }}" class="text-sm font-semibold text-blue-600 underline decoration-mist underline-offset-4 hover:decoration-blue-600" title="Upload category image">
                                            📸 Image
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-graphite">You have not submitted a category request yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
