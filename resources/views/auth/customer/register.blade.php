@extends('layouts.auth')
@section('title', 'Create an account — VandoraX')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[28px] leading-tight text-white">Buy from shops that have been checked.</h2>
        <p class="mt-4 text-[15px] leading-relaxed text-forest-100/80">
            Every seller on VandoraX submits their CNIC, tax details and bank account before their
            first listing goes live.
        </p>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">

    <h1 class="text-[28px] leading-tight">Create your account</h1>
    <p class="mt-1 text-[15px] text-graphite">Takes about a minute.</p>

    <hr class="my-7 border-mist">

    <form method="POST" action="{{ route('customer.register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="field-label">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   class="field @error('name') field-invalid @enderror">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                   class="field @error('email') field-invalid @enderror">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone_number" class="field-label">
                Mobile <span class="font-normal text-graphite/60">— optional</span>
            </label>
            <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}"
                   placeholder="0300 1234567"
                   class="field @error('phone_number') field-invalid @enderror">
            <p class="field-hint">We use this for delivery updates only.</p>
            @error('phone_number')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="field-label">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       class="field @error('password') field-invalid @enderror">
            </div>
            <div>
                <label for="password_confirmation" class="field-label">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       autocomplete="new-password" class="field">
            </div>
        </div>
        <p class="field-hint -mt-3">8 characters or more, with a capital letter and a number.</p>
        @error('password')<p class="field-error -mt-3">{{ $message }}</p>@enderror

        <label class="flex items-start gap-2.5 text-sm text-graphite">
            <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}
                   class="mt-0.5 h-4 w-4 rounded-sm border-mist text-forest focus:ring-marigold">
            <span>I accept the terms of service and privacy policy.</span>
        </label>
        @error('terms')<p class="field-error">{{ $message }}</p>@enderror

        <button type="submit" class="btn-primary">Create account</button>
    </form>
</div>

<p class="mt-6 text-center text-sm text-graphite">
    Already have one? <a href="{{ route('customer.login') }}" class="btn-quiet">Log in</a>
</p>
@endsection