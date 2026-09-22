@extends('layouts.auth')
@section('title', 'Choose a new password — VandoraX')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Nearly done.</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            Once you save this, you'll be signed out everywhere else.
        </p>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">

    <h1 class="text-[26px] leading-tight">Choose a new password</h1>

    <hr class="my-7 border-mist">

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required readonly
                   class="field bg-paper text-graphite">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="field-label">New password</label>
            <input id="password" name="password" type="password" required autofocus autocomplete="new-password"
                   class="field @error('password') field-invalid @enderror">
            <p class="field-hint">8 characters or more, with a capital letter and a number.</p>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="field-label">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   autocomplete="new-password" class="field">
        </div>

        <button type="submit" class="btn-primary">Save new password</button>
    </form>
</div>
@endsection