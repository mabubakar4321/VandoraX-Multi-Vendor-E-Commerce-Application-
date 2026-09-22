@extends('layouts.app', ['logoutRoute' => route('customer.logout')])
@section('title', 'Your account — VandoraX')

@section('content')
    <h1 class="text-[32px] leading-tight">Hello, {{ explode(' ', auth()->user()->name)[0] }}</h1>
    <p class="mt-2 max-w-prose text-[15px] text-graphite">
        Your orders, saved items and addresses will live here.
    </p>

    <div class="mt-10 border border-mist bg-white px-6 py-14 text-center" style="border-radius: 2px;">
        <h2 class="text-[20px] leading-tight">Nothing ordered yet</h2>
        <p class="mx-auto mt-2 max-w-prose text-[15px] text-graphite">
            When you place your first order it'll show up here, with tracking.
        </p>
        <a href="{{ route('home') }}" class="btn-primary mx-auto mt-6 w-auto px-6">Start browsing</a>
        <a href="{{ route('storefront.products.index') }}" class="btn-secondary mx-auto mt-3 w-auto px-6">Browse approved products</a>
    </div>
@endsection