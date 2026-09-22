<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — VandoraX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-forest">

<div class="flex min-h-screen items-center justify-center px-5 py-12">
    <div class="w-full max-w-sm">

        <div class="border-t-2 border-marigold bg-white p-8" style="border-radius: 2px;">

            <span class="text-lg font-display font-bold text-forest">
                Vandora<span class="text-marigold-600">X</span>
            </span>
            <h1 class="mt-5 text-[22px] leading-tight">Staff sign-in</h1>
            <p class="mt-1 text-sm text-graphite">This area is for platform administrators.</p>

            @if ($errors->has('message'))
                <div class="alert alert-error mt-6" role="alert">{{ $errors->first('message') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="mt-7 space-y-5">
                @csrf

                <div>
                    <label for="email" class="field-label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           required autofocus autocomplete="email"
                           class="field @error('email') field-invalid @enderror">
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="field-label">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="field @error('password') field-invalid @enderror">
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2.5 text-sm text-graphite">
                    <input type="checkbox" name="remember" value="1"
                           class="h-4 w-4 rounded-sm border-mist text-forest focus:ring-marigold">
                    Stay signed in
                </label>

                <button type="submit" class="btn-dark">Sign in</button>
            </form>
        </div>

        <p class="mt-5 text-center text-[13px] text-forest-100/50">
            Sign-in attempts are logged with your IP address.
        </p>
    </div>
</div>

</body>
</html>