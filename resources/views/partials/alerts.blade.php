@if (session('success'))
    <div class="alert alert-success" role="status">{{ session('success') }}</div>
@endif

@if (session('warning'))
    <div class="alert alert-warning" role="status">{{ session('warning') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-error" role="alert">{{ session('error') }}</div>
@endif

@if ($errors->has('message'))
    <div class="alert alert-error" role="alert">{{ $errors->first('message') }}</div>
@endif