@extends('layouts.auth')
@section('title', 'Verify your email — VandoraX')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Check your inbox.</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            Verifying your address keeps your account, and your orders, yours.
        </p>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">

    <h1 class="text-[26px] leading-tight">One link to click</h1>
    <p class="mt-3 max-w-prose text-[15px] leading-relaxed text-graphite">
        We sent it @if (session('pending_email'))
            to <span class="font-medium text-forest">{{ session('pending_email') }}</span>
        @endif. It works for the next 60 minutes.
    </p>

    <hr class="my-7 border-mist">

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="field-label">Send it again</label>
            <input id="email" name="email" type="email" required
                   value="{{ session('pending_email') ?? old('email') }}"
                   class="field">
        </div>
        <button type="submit" class="btn-primary">Resend the link</button>
    </form>

    <p class="mt-6 text-[13px] leading-relaxed text-graphite/70">
        Nothing arrived? Check your spam folder first — that's where it usually is.
    </p>
</div>
@endsection