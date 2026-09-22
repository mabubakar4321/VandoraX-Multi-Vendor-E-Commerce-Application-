@extends('layouts.vendor')

@section('title', 'Request a Category — VandoraX')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('vendor.categories.requests') }}" class="text-sm font-medium text-forest underline decoration-mist underline-offset-4 hover:decoration-forest">Back to category requests</a>
        <h1 class="mt-6 text-[32px] leading-tight">Request a category</h1>
        <p class="mt-2 text-[15px] text-graphite">Tell our admin team which category your products need.</p>

        <form method="POST" action="{{ route('vendor.categories.request.store') }}" class="mt-8 space-y-6 border border-mist bg-white p-6 sm:p-8" style="border-radius: 2px;">
            @csrf
            <div>
                <label for="category_name" class="field-label">Category name</label>
                <input id="category_name" type="text" name="category_name" value="{{ old('category_name') }}" required class="field" placeholder="e.g. Home office accessories">
                @error('category_name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="field-label">Reason or description</label>
                <textarea id="description" name="description" rows="5" required class="field" placeholder="Explain why this category is needed for your products.">{{ old('description') }}</textarea>
                @error('description') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="border-l-[3px] border-marigold bg-marigold-100 px-4 py-3 text-sm leading-relaxed text-forest">
                Requests are reviewed by the admin team. You will receive an email when a decision is made.
            </div>

            <div class="flex flex-wrap gap-3 border-t border-mist pt-6">
                <button type="submit" class="btn-primary w-auto px-5">Submit request</button>
                <a href="{{ route('vendor.categories.requests') }}" class="inline-flex items-center justify-center border border-mist bg-white px-5 py-3.5 text-[15px] font-semibold text-forest hover:bg-mist" style="border-radius: 2px;">Cancel</a>
            </div>
        </form>
    </div>
@endsection
