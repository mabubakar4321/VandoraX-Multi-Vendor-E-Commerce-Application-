@extends('layouts.app')
@section('title', 'My Complaints')
@section('content')
<div class="space-y-7">
    @if (session('success'))<div class="rounded border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[.18em] text-graphite/60">Customer support</p><h1 class="mt-2 text-3xl leading-tight sm:text-4xl">My Complaints</h1><p class="mt-3 max-w-2xl text-sm leading-6 text-graphite/70">Track complaints submitted to the admin team and read any response.</p></div><a class="btn-primary w-full text-center sm:w-auto" href="{{ route('customer.complaints.create') }}">Submit new complaint</a></div>
    <div class="grid gap-4">
        @forelse ($complaints as $complaint)
            <article class="panel p-5"><div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[.14em] text-graphite/60">{{ $complaint->complaint_number }}</p><h2 class="mt-1 text-lg text-forest">{{ $complaint->subject }}</h2><p class="mt-1 text-sm text-graphite/65">{{ $complaint->category }} · Submitted {{ $complaint->created_at?->format('M d, Y H:i') }}</p></div><div class="flex items-center gap-2 sm:flex-col sm:items-end"><span class="status-pill status-{{ $complaint->status }}">{{ str_replace('_', ' ', ucfirst($complaint->status)) }}</span><span class="text-xs font-semibold capitalize text-graphite/60">{{ $complaint->priority }} priority</span></div></div><p class="mt-5 whitespace-pre-line text-sm leading-6 text-graphite/75">{{ $complaint->description }}</p>@if($complaint->admin_notes)<div class="mt-5 border-t border-mist pt-4"><p class="text-xs font-semibold uppercase tracking-[.14em] text-graphite/60">Admin response</p><p class="mt-2 whitespace-pre-line text-sm text-graphite/75">{{ $complaint->admin_notes }}</p>@if($complaint->reviewed_at)<p class="mt-2 text-xs text-graphite/60">Reviewed {{ $complaint->reviewed_at->format('M d, Y H:i') }}</p>@endif</div>@endif</article>
        @empty
            <div class="panel p-10 text-center"><h2 class="text-lg font-semibold text-forest">No complaints submitted</h2><p class="mt-2 text-sm text-graphite/65">Submit a complaint if you need help from the admin team.</p><a class="btn-primary mt-5 inline-flex" href="{{ route('customer.complaints.create') }}">Submit first complaint</a></div>
        @endforelse
    </div>
    {{ $complaints->links() }}
</div>
@endsection
