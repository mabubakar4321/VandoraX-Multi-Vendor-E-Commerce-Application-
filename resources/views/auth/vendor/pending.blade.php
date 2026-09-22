@extends('layouts.auth')

@section('title', 'Application status — VandoraX')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Your seller application.</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            We are reviewing your store details and documents.
        </p>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">
    <h1 class="text-[28px] leading-tight">{{ $vendor->store_name }}</h1>
    <p class="mt-2 text-[15px] text-graphite">Your application is currently under review.</p>

    <div class="mt-7 border border-mist bg-forest-100/20 p-5">
        <p class="text-sm font-medium text-forest">Application status</p>
        <p class="mt-1 text-lg font-semibold capitalize text-forest">
            {{ str_replace('_', ' ', $vendor->status) }}
        </p>
        <p class="mt-3 text-sm leading-relaxed text-graphite">
            We will email you when your store has been reviewed. You can return here after approval to access your seller dashboard.
        </p>
    </div>

    <form method="POST" action="{{ route('vendor.logout') }}" class="mt-7">
        @csrf
        <button type="submit" class="btn-primary">Log out</button>
    </form>
</div>
@endsection