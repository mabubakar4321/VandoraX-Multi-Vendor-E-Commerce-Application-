@extends('layouts.app')
@section('title', 'Request a Return')
@section('content')
<div class="mx-auto max-w-3xl space-y-6 px-4 py-8 sm:px-6">
    <div><a href="{{ route('order.show', $order) }}" class="text-sm font-semibold text-forest">← Back to order</a><p class="mt-5 text-xs font-semibold uppercase tracking-[.18em] text-graphite/60">Return management</p><h1 class="mt-2 text-3xl">Request a return</h1><p class="mt-2 text-sm text-graphite/70">You can request a return at any time after this item is delivered.</p></div>
    <section class="panel p-5 sm:p-7">
        <div class="mb-6 flex items-center gap-4 border-b border-mist pb-5"><div class="h-16 w-16 overflow-hidden rounded border border-mist bg-paper">@if($item->product?->mainImage())<img class="h-full w-full object-contain" src="{{ asset('storage/'.$item->product->mainImage()->image_path) }}" alt="">@endif</div><div><h2 class="text-lg">{{ $item->product->name }}</h2><p class="text-sm text-graphite/65">Quantity {{ $item->quantity }} · ₨{{ number_format($item->total_amount, 2) }}</p></div></div>
        <form method="POST" action="{{ route('returns.store', [$order, $item]) }}" enctype="multipart/form-data" class="space-y-5">@csrf
            <div><label class="field-label" for="quantity">Quantity to return</label><input id="quantity" name="quantity" type="number" class="field" min="1" max="{{ $item->quantity }}" value="{{ old('quantity', $item->quantity) }}" required><p class="field-hint">You can return part or all of this item quantity.</p></div>
            <div><label class="field-label" for="reason">Reason</label><select id="reason" name="reason" class="field" required><option value="">Select a reason</option>@foreach(['wrong_size'=>'Wrong Size','defective'=>'Defective','damaged'=>'Damaged','changed_mind'=>'Changed Mind','not_as_described'=>'Not as Described','other'=>'Other'] as $value=>$label)<option value="{{ $value }}" @selected(old('reason')===$value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="field-label" for="description">Additional comments</label><textarea id="description" name="description" rows="5" class="field" required minlength="10" placeholder="Explain the issue clearly...">{{ old('description') }}</textarea></div>
            <div><label class="field-label" for="photos">Photos of the issue <span class="font-normal text-graphite/60">(optional, up to 5)</span></label><input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="field"><p class="field-hint">Photos are recommended for defective, damaged, or not-as-described claims.</p></div>
            <button type="submit" class="btn-primary">Submit return request</button>
        </form>
    </section>
</div>
@endsection
