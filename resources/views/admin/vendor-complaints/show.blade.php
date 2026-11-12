@extends('layouts.admin')
@section('title', 'Review '.$vendorComplaint->complaint_number)
@section('content')
<div class="space-y-7">
    <div><a class="text-sm font-semibold text-forest" href="{{ route('admin.vendor-complaints.index') }}">← Back to vendor complaints</a><h1 class="mt-4 text-3xl">{{ $vendorComplaint->complaint_number }}</h1><p class="mt-2 text-sm text-graphite/70">{{ $vendorComplaint->subject }}</p></div>
    <div class="grid gap-6 lg:grid-cols-[1fr_21rem]">
        <main class="space-y-6">
            <section class="panel p-5"><h2 class="text-xl">Complaint details</h2><dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2"><div><dt class="text-graphite/60">Subject</dt><dd class="font-semibold">{{ $vendorComplaint->subject }}</dd></div><div><dt class="text-graphite/60">Category</dt><dd class="font-semibold">{{ $vendorComplaint->category }}</dd></div><div><dt class="text-graphite/60">Submitted</dt><dd class="font-semibold">{{ $vendorComplaint->created_at?->format('M d, Y H:i') }}</dd></div><div><dt class="text-graphite/60">Priority</dt><dd class="font-semibold capitalize">{{ $vendorComplaint->priority }}</dd></div></dl><p class="mt-6 whitespace-pre-line text-sm leading-7 text-graphite/80">{{ $vendorComplaint->description }}</p></section>
            @if ($vendorComplaint->admin_notes)<section class="panel p-5"><h2 class="text-xl">Admin notes</h2><p class="mt-3 whitespace-pre-line text-sm leading-7 text-graphite/75">{{ $vendorComplaint->admin_notes }}</p><p class="mt-3 text-xs text-graphite/60">Reviewed {{ $vendorComplaint->reviewed_at?->format('M d, Y H:i') }}</p></section>@endif
        </main>
        <aside class="panel h-fit p-5"><h2 class="text-lg">Vendor</h2><dl class="mt-4 space-y-3 text-sm"><div><dt class="text-graphite/60">Store</dt><dd class="font-semibold">{{ $vendorComplaint->vendor->store_name ?? '—' }}</dd></div><div><dt class="text-graphite/60">Contact</dt><dd class="font-semibold">{{ $vendorComplaint->vendor->user->name ?? '—' }}</dd></div><div><dt class="text-graphite/60">Email</dt><dd class="break-all font-semibold">{{ $vendorComplaint->vendor->user->email ?? '—' }}</dd></div><div><dt class="text-graphite/60">Status</dt><dd class="font-semibold capitalize">{{ str_replace('_', ' ', $vendorComplaint->status) }}</dd></div></dl></aside>
    </div>
</div>
@endsection
