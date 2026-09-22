@extends('layouts.auth')
@section('title', 'Reset your password — VandoraX')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Happens to everyone.</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            This works for customer, seller and staff accounts alike.
        </p>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">

    <h1 class="text-[26px] leading-tight">Reset your password</h1>
    <p class="mt-3 max-w-prose text-[15px] leading-relaxed text-graphite">
        Enter your email and we'll send you a link to set a new one. The link lasts an hour.
    </p>

    <hr class="my-7 border-mist">

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="field @error('email') field-invalid @enderror">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-primary">Send the link</button>
    </form>
</div>

<p class="mt-6 text-center text-sm text-graphite">
    <a href="{{ route('customer.login') }}" class="btn-quiet">Customer log in</a>
    <span class="px-2 text-mist">/</span>
    <a href="{{ route('vendor.login') }}" class="btn-quiet">Seller log in</a>
</p>
@endsection