@props(['product'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
    <!-- Image -->
    <div class="relative bg-gray-200 h-48 overflow-hidden">
        @if($product->images->first())
            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
        
        <!-- Badge -->
        @if($product->status === 'draft')
            <span class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 text-xs font-semibold rounded">Draft</span>
        @endif
    </div>

    <!-- Content -->
    <div class="p-4">
        <h3 class="font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
        <p class="text-sm text-gray-600 mt-1">SKU: {{ $product->sku }}</p>
        
        <div class="mt-3">
            <p class="text-lg font-bold text-indigo-600">₨{{ number_format($product->base_price) }}</p>
            <p class="text-xs text-gray-500">Stock: {{ $product->getTotalStock() }}</p>
        </div>
    </div>
</div>