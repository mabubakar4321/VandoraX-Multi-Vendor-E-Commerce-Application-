@extends('layouts.auth')
@section('title', 'Sell on VandoraX — Step 3')
@section('width', 'max-w-lg')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Last step.</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            We check every document by hand. Clear photos are approved faster than scans.
        </p>
        <div class="mt-10">
            @include('auth.vendor.partials.progress', ['current' => 3])
        </div>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">

    <p class="text-[13px] font-medium text-graphite lg:hidden">Step 3 of 3</p>
    <h1 class="mt-1 text-[28px] leading-tight">Your store</h1>
    <p class="mt-1 text-[15px] text-graphite">How customers will find you.</p>

    <hr class="my-7 border-mist">

    {{-- Error Summary --}}
    @if ($errors->any())
        <div class="mb-6 rounded border border-red-200 bg-red-50 p-4">
            <p class="text-sm font-medium text-red-900">Please fix the following errors:</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-800">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.register.step3.store') }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Store Information --}}
        <div>
            <label for="store_name" class="field-label">Store name</label>
            <input id="store_name" name="store_name" type="text" value="{{ old('store_name') }}" required autofocus
                   class="field @error('store_name') field-invalid @enderror">
            <p class="field-hint">Customers see this on every listing. It can't be changed later.</p>
            @error('store_name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="category_id" class="field-label">What do you mainly sell?</label>
            <select id="category_id" name="category_id" required
                    class="field @error('category_id') field-invalid @enderror">
                <option value="">Choose a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="store_description" class="field-label">
                About your store <span class="font-normal text-graphite/60">— optional</span>
            </label>
            <textarea id="store_description" name="store_description" rows="3" maxlength="1000"
                      class="field @error('store_description') field-invalid @enderror">{{ old('store_description') }}</textarea>
            <p class="field-hint">Tell customers what makes you unique. Max 1000 characters.</p>
            @error('store_description')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- Documents Section --}}
        <div class="border-t border-mist pt-6">
            <h2 class="text-[20px] leading-tight">Documents</h2>
            <p class="mt-1 text-[13px] text-graphite">JPG, PNG, or PDF files only. Up to 2 MB each.</p>

            <div class="mt-5 divide-y divide-mist border-y border-mist">
                @foreach ([
                    'cnic_front'      => ['CNIC, front side', 'All four corners must be clearly visible', true],
                    'cnic_back'       => ['CNIC, back side', 'All four corners must be clearly visible', true],
                    'bank_proof'      => ['Bank proof', 'A cancelled cheque or bank statement header showing your name and account number', true],
                    'tax_certificate' => ['NTN certificate', 'Only required if you are registered for tax', false],
                ] as $field => [$label, $caption, $required])
                    <div class="py-4">
                        <label for="{{ $field }}" class="flex items-baseline justify-between gap-4">
                            <span class="text-[15px] font-medium text-forest">
                                {{ $label }}
                                @if ($required)
                                    <span class="text-red-600">*</span>
                                @endif
                            </span>
                            @unless ($required)
                                <span class="shrink-0 text-[13px] text-graphite/60">Optional</span>
                            @endunless
                        </label>
                        <p class="mt-0.5 max-w-prose text-[13px] leading-relaxed text-graphite/70">{{ $caption }}</p>

                        <input id="{{ $field }}" name="{{ $field }}" type="file"
                               accept=".jpg,.jpeg,.png,.pdf" @required($required)
                               class="mt-2.5 w-full text-sm text-graphite
                                      file:mr-4 file:border-0 file:bg-forest file:px-4 file:py-2
                                      file:text-sm file:font-medium file:text-white
                                      hover:file:bg-forest-700 focus:outline-none focus:ring-2 focus:ring-forest/20">
                        @error($field)<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Security & Info Box --}}
        <div class="rounded border border-mist bg-forest-100/20 px-4 py-3">
            <p class="text-[13px] font-medium text-forest">✓ Document tips</p>
            <ul class="mt-2 space-y-1 text-[13px] text-forest/70">
                <li>• Take clear photos in good lighting</li>
                <li>• Make sure all corners are visible</li>
                <li>• File must be under 2 MB</li>
                <li>• We verify documents within 2 working days</li>
            </ul>
        </div>

        <button type="submit" class="btn-primary w-full">Submit application</button>
        <p class="text-center text-[13px] text-graphite">
            You'll hear from us within two working days.
        </p>
    </form>

</div>

<p class="mt-6 text-center text-sm text-graphite">
    <a href="{{ route('vendor.register.step2') }}" class="font-medium text-forest hover:underline">← Back to Step 2</a>
</p>
@endsection