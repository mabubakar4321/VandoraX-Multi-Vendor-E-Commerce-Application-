@extends('layouts.vendor')
@section('title', 'Send Complaint — Seller Centre')
@section('content')
<div class="mx-auto max-w-3xl space-y-7">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-graphite/60">Seller support</p>
            <h1 class="mt-2 text-3xl leading-tight sm:text-4xl">Send a Complaint</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-graphite/70">Tell the admin team what happened. Include enough detail so the issue can be reviewed and resolved quickly.</p>
        </div>
        <a class="btn-secondary w-full shrink-0 text-center sm:w-auto" href="{{ route('vendor.complaints.index') }}">My complaints</a>
    </div>

    @if ($errors->any())
        <div class="rounded border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('vendor.complaints.store') }}" class="panel space-y-6 p-5 sm:p-7">
        @csrf
        <div><label class="label" for="subject">Complaint subject</label><input class="field" id="subject" name="subject" value="{{ old('subject') }}" maxlength="180" required placeholder="Short summary of the issue"></div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="label" for="category">Category</label><select class="field" id="category" name="category" required><option value="">Select category</option>@foreach(['Orders','Payments','Returns and refunds','Customers','Products','Delivery','Account','Other'] as $category)<option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>@endforeach</select></div>
            <div><label class="label" for="priority">Priority</label><select class="field" id="priority" name="priority" required>@foreach(['low','medium','high','urgent'] as $priority)<option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>{{ ucfirst($priority) }}</option>@endforeach</select></div>
        </div>
        <div><label class="label" for="description">Complaint details</label><textarea class="field min-h-48" id="description" name="description" minlength="20" maxlength="5000" required placeholder="Explain the issue, relevant order or product numbers, what you have already tried, and what resolution you are requesting.">{{ old('description') }}</textarea><p class="mt-2 text-xs text-graphite/60">Do not include passwords, card numbers, or other sensitive account credentials.</p></div>
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a class="btn-secondary text-center" href="{{ route('vendor.dashboard') }}">Cancel</a><button class="btn-primary" type="submit">Send complaint to admin</button></div>
    </form>
</div>
@endsection
