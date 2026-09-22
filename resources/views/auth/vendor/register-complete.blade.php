@extends('layouts.auth')
@section('title', 'Application submitted — VandoraX')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Application received.</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            We will review your documents and store details carefully.
        </p>
    </div>
@endsection

@section('content')
<div class="panel p-7 text-center sm:p-9">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-forest text-2xl text-white" aria-hidden="true">
        ✓
    </div>

    <h1 class="mt-6 text-[28px] leading-tight">Your application is submitted</h1>
    <p class="mx-auto mt-3 max-w-md text-[15px] leading-relaxed text-graphite">
        Thanks for applying to sell on VandoraX. Our team will review your documents and notify you when your store is approved.
    </p>

    <a href="{{ route('vendor.login') }}" class="btn-primary mt-7 inline-flex">
        Go to seller login
    </a>
</div>
@endsection
