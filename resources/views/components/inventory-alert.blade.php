@props(['product'])

@php


    $stock = $product->getTotalStock();
    $type = $stock === 0 ? 'out' : ($stock < 5 ? 'low' : 'ok');
@endphp

<div class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ 
    $type === 'out' ? 'bg-red-100 text-red-800' : 
    ($type === 'low' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') 
}}">
    {{ $type === 'out' ? 'Out of Stock' : ($type === 'low' ? 'Low Stock' : 'In Stock') }}
</div>