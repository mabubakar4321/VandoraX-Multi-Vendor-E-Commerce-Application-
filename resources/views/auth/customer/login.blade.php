@extends('layouts.auth')
@section('title', 'Log in — VandoraX')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[28px] leading-tight text-white">Everything from your city's sellers, in one place.</h2>
        <p class="mt-4 text-[15px] leading-relaxed text-forest-100/80">
            Over 2,000 verified shops. One cart, one checkout, one place to track it all.
        </p>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">

    <h1 class="text-[28px] leading-tight">Log in</h1>
    <p class="mt-1 text-[15px] text-graphite">Welcome back.</p>

    <hr class="my-7 border-mist">

    <form method="POST" action="{{ route('customer.login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}"
                   required autofocus autocomplete="email"
                   class="field @error('email') field-invalid @enderror">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="mb-1.5 flex items-baseline justify-between">
                <label for="password" class="field-label mb-0">Password</label>
                <a href="{{ route('password.request') }}" class="text-[13px] text-graphite hover:text-forest">Forgot it?</a>
            </div>
            <input id="password" name="password" type="password"
                   required autocomplete="current-password"
                   class="field @error('password') field-invalid @enderror">
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <label class="flex items-center gap-2.5 text-sm text-graphite">
            <input type="checkbox" name="remember" value="1"
                   class="h-4 w-4 rounded-sm border-mist text-forest focus:ring-marigold">
            Stay logged in for 30 days
        </label>

        <button type="submit" class="btn-primary">Log in</button>
    </form>

    @if (session('show_resend_for'))
        <form method="POST" action="{{ route('verification.send') }}" class="mt-5 border-t border-mist pt-5">
            @csrf
            <input type="hidden" name="email" value="{{ session('show_resend_for') }}">
            <p class="text-sm text-graphite">
                Not verified yet?
                <button type="submit" class="btn-quiet">Send the link again</button>
            </p>
        </form>
    @endif
</div>

<p class="mt-6 text-center text-sm text-graphite">
    New here? <a href="{{ route('customer.register') }}" class="btn-quiet">Create an account</a>
</p>
@endsection