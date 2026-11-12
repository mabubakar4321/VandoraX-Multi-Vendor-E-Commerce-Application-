@extends('layouts.vendor')
@section('title', 'Appeal for Refund — Seller Centre')
@section('content')
<div class="mx-auto max-w-4xl space-y-7">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[.18em] text-graphite/60">Seller finance</p>
        <h1 class="mt-2 text-3xl leading-tight sm:text-4xl">Appeal for Refund</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-graphite/70">Send the admin team a complete refund request for a customer return. The admin will review the order, return evidence, and your explanation.</p>
        <div class="mt-5">
            <a class="btn-secondary w-full text-center sm:w-auto" href="{{ route('vendor.refund-appeals.index') }}">Show all appeals</a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('vendor.refund-appeals.store') }}" class="panel space-y-6 p-5 sm:p-7">
        @csrf
        <div>
            <label class="label" for="return_id">Return request</label>
            <select class="field" id="return_id" name="return_id" required>
                <option value="">Select the customer return</option>
                @foreach ($returns as $return)
                    <option value="{{ $return->id }}" @selected(old('return_id') == $return->id)>
                        {{ $return->return_number }} · {{ $return->orderItem->product->name ?? 'Product' }} · {{ $return->orderItem->order->customer->name ?? 'Customer' }}
                    </option>
                @endforeach
            </select>
            @if ($returns->isEmpty())<p class="mt-2 text-sm text-graphite/60">There are no eligible customer returns for a refund appeal.</p>@endif
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="label" for="requested_amount">Requested refund amount</label><input class="field" id="requested_amount" name="requested_amount" type="number" min="0.01" step="0.01" value="{{ old('requested_amount') }}" required placeholder="0.00"></div>
            <div><label class="label" for="refund_method">Suggested refund method</label><input class="field" id="refund_method" name="refund_method" value="{{ old('refund_method') }}" maxlength="100" placeholder="Original card / wallet / other"></div>
        </div>
        <div><label class="label" for="refund_details">Refund details</label><textarea class="field min-h-24" id="refund_details" name="refund_details" maxlength="2000" placeholder="Add any transaction, account, or processing details the admin should know.">{{ old('refund_details') }}</textarea></div>
        <div><label class="label" for="reason">Reason for refund request</label><textarea class="field min-h-24" id="reason" name="reason" maxlength="1000" required placeholder="Why should this customer receive a refund?">{{ old('reason') }}</textarea></div>
        <div><label class="label" for="seller_explanation">Complete appeal explanation</label><textarea class="field min-h-40" id="seller_explanation" name="seller_explanation" minlength="10" maxlength="4000" required placeholder="Explain the situation, the product condition, communication with the customer, and the action you want the admin to take.">{{ old('seller_explanation') }}</textarea></div>
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a class="btn-secondary text-center" href="{{ route('vendor.returns.index') }}">Back to returns</a>
            <button class="btn-primary" type="submit" @disabled($returns->isEmpty())>Submit refund appeal</button>
        </div>
    </form>
</div>
@endsection
