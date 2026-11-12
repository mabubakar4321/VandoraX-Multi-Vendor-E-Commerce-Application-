@extends('layouts.admin')

@section('title', 'Category Request')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.category-requests.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">← Back to category requests</a>
            <p class="mt-5 text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Category request review</p>
            <h1 class="mt-2 break-words text-3xl leading-tight">{{ $request->category_name }}</h1>
            <p class="mt-2 text-sm text-graphite/70">Review the vendor's proposal and record a decision.</p>
        </div>
        <span class="inline-flex self-start rounded-full px-3 py-1.5 text-sm font-semibold capitalize {{ $request->isPending() ? 'bg-marigold-100 text-forest' : ($request->isApproved() ? 'bg-forest-100 text-forest' : 'bg-vermilion-100 text-vermilion') }}">
            {{ ucfirst($request->status) }}
        </span>
    </div>

    <div class="grid min-w-0 gap-6 xl:grid-cols-3">
        <section class="min-w-0 border border-mist bg-white p-5 sm:p-6 xl:col-span-2" style="border-radius: 2px;">
            <h2 class="text-xl">Request details</h2>
            <dl class="mt-6 space-y-5 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Category name</dt>
                    <dd class="mt-1 font-semibold text-forest">{{ $request->category_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Description</dt>
                    <dd class="mt-1 break-words whitespace-pre-line leading-relaxed text-graphite">{{ $request->description ?: 'No description provided.' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Requested</dt>
                    <dd class="mt-1 text-forest">{{ optional($request->requested_at)->format('M d, Y g:i A') ?: 'Unknown' }}</dd>
                </div>
                @if($request->admin_comment)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Admin comment</dt>
                        <dd class="mt-1 break-words whitespace-pre-line text-graphite">{{ $request->admin_comment }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <aside class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
            <h2 class="text-xl">Vendor</h2>
            <dl class="mt-6 space-y-5 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Name</dt>
                    <dd class="mt-1 font-medium text-forest">{{ optional($request->vendor->user)->name ?: 'Unknown' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Email</dt>
                    <dd class="mt-1 break-all text-forest">{{ optional($request->vendor->user)->email ?: 'Unknown' }}</dd>
                </div>
            </dl>
        </aside>
    </div>

    @if($request->isPending())
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="border border-forest-100 border-t-[3px] border-t-forest-500 bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Approve request</h2>
                <p class="mt-1 text-sm text-graphite/70">Approving creates this category and notifies the vendor.</p>
                <form method="POST" action="{{ route('admin.category-requests.approve', $request) }}" class="mt-5">
                    @csrf
                    <label for="comment" class="field-label">Comment (optional)</label>
                    <textarea id="comment" name="comment" rows="3" maxlength="500" class="field @error('comment') field-invalid @enderror">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn-primary mt-4 w-auto px-5 py-2.5">
                        Approve request
                    </button>
                </form>
            </section>

            <section class="border border-vermilion-100 border-t-[3px] border-t-vermilion bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl text-vermilion">Reject request</h2>
                <p class="mt-1 text-sm text-graphite/70">Provide a reason so the vendor understands the decision.</p>
                <form method="POST" action="{{ route('admin.category-requests.reject', $request) }}" class="mt-5">
                    @csrf
                    <label for="reason" class="field-label">Reason</label>
                    <textarea id="reason" name="reason" rows="3" maxlength="500" required class="field @error('reason') field-invalid @enderror">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-4 inline-flex w-full items-center justify-center bg-vermilion px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-vermilion/90 sm:w-auto">
                        Reject request
                    </button>
                </form>
            </section>
        </div>
    @endif
</div>
@endsection
