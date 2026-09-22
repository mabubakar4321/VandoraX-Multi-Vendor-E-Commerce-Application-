@extends('layouts.auth')
@section('title', 'Sell on VandoraX — Step 1')
@section('width', 'max-w-lg')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Start your shop.</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            Tell us a little about yourself before we set up your seller account.
        </p>
        <div class="mt-10">
            @include('auth.vendor.partials.progress', ['current' => 1])
        </div>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">
    <p class="text-[13px] font-medium text-graphite lg:hidden">Step 1 of 3</p>
    <h1 class="mt-1 text-[28px] leading-tight">Your details</h1>
    <p class="mt-1 text-[15px] text-graphite">Create your seller account to get started.</p>

    <hr class="my-7 border-mist">

    @if($errors->any())
        <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-red-700" style="border-radius: 2px;">
            <p class="font-semibold">{{ $errors->first('message') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.register.step1') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="field-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   autocomplete="name" class="field @error('name') field-invalid @enderror"
                   placeholder="Your full name">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="field-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   autocomplete="email" class="field @error('email') field-invalid @enderror"
                   placeholder="seller@example.com">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone_number" class="field-label">Phone number</label>
            <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" required
                   autocomplete="tel" class="field @error('phone_number') field-invalid @enderror"
                   placeholder="03XX-XXXXXXX or +92-3XX-XXXXXXX">
            @error('phone_number')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="field-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="field @error('password') field-invalid @enderror"
                   placeholder="Min 8 chars, uppercase, lowercase, number, symbol">
            <p class="field-hint">Use uppercase, lowercase, numbers, and symbols.</p>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="field-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   autocomplete="new-password" class="field" placeholder="Confirm your password">
        </div>

        <label class="flex items-start gap-2.5 text-sm text-graphite">
            <input id="terms" type="checkbox" name="terms" required
                   class="mt-0.5 h-4 w-4 rounded-sm border-mist text-forest focus:ring-marigold">
            <span>I agree to the <a href="#" class="text-forest hover:underline">Terms of Service</a></span>
        </label>

        <button type="submit" class="btn-primary">Continue to Step 2</button>
    </form>
</div>

<p class="mt-6 text-center text-sm text-graphite">
    Already have an account? <a href="{{ route('vendor.login') }}" class="btn-quiet">Log in here</a>
</p>
@endsection