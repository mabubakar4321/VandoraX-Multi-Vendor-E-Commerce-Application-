@extends('layouts.vendor')

@section('title', 'My Categories — VandoraX')

@section('content')
<div class="mb-8">
    <p class="text-sm font-medium text-forest-500">Seller Centre</p>
    <h1 class="mt-1 text-[32px] leading-tight">My Categories</h1>
    <p class="mt-2 text-[15px] text-graphite">
        Categories assigned to <span class="font-semibold text-forest">{{ $vendor->store_name }}</span> — 4 categories
    </p>
</div>

@if($categories->isEmpty())
    <div class="border border-mist bg-white p-12 text-center" style="border-radius: 2px;">
        <p class="text-graphite">No categories assigned to your store yet.</p>
    </div>
@else
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-2">
        @foreach($categories as $category)
            <div class="group overflow-hidden border border-mist bg-white hover:shadow-md transition-shadow" style="border-radius: 2px;">
                <button type="button"
                        class="cat-card w-full text-left"
                        data-img="{{ $category->icon ? asset('storage/' . $category->icon) : '' }}"
                        data-name="{{ $category->name }}"
                        aria-label="View {{ $category->name }} image">
                    <div class="relative h-48 overflow-hidden bg-paper">
                        @if($category->icon)
                            <img src="{{ asset('storage/' . $category->icon) }}"
                                 alt="{{ $category->name }}"
                                 class="h-full w-full object-cover group-hover:scale-[1.04] transition-transform duration-500"
                                 loading="lazy"
                                 onerror="this.style.display='none'">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-forest-100 text-forest-500">
                                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a2 2 0 012-2z"/></svg>
                            </div>
                        @endif
                        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-forest backdrop-blur">
                            {{ $category->commission_rate }}% commission
                        </span>
                    </div>
                </button>
                <div class="p-4">
                    <h3 class="font-semibold text-forest">{{ $category->name }}</h3>
                    @if($category->description)
                        <p class="mt-1 text-sm leading-relaxed text-graphite/80">{{ $category->description }}</p>
                    @endif
                    <p class="mt-2 text-xs text-graphite/60">{{ $category->slug }}</p>
                    <a href="{{ route('vendor.categories.edit', $category) }}" class="mt-3 inline-flex items-center gap-1.5 border border-mist bg-white px-3 py-1.5 text-xs font-semibold text-forest hover:bg-paper" style="border-radius: 2px;">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Lightbox --}}
<div id="vendorCatLightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-forest/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Category image">
    <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25" aria-label="Close" onclick="closeVendorCatLb()">
        <span class="text-2xl leading-none">×</span>
    </button>
    <img id="vendorCatLightboxImg" src="" alt="" class="max-h-[86vh] max-w-[92vw] rounded-lg bg-white shadow-2xl">
    <p id="vendorCatLightboxCaption" class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/40 px-4 py-1.5 text-sm font-semibold text-white"></p>
</div>

<script>
(()=> {
    const lb = document.getElementById('vendorCatLightbox');
    const lbImg = document.getElementById('vendorCatLightboxImg');
    const lbCap = document.getElementById('vendorCatLightboxCaption');
    function openLb(src, name){
        if(!src) return;
        lbImg.src = src; lbImg.alt = name || 'Category image'; lbCap.textContent = name || '';
        lb.classList.remove('hidden'); lb.classList.add('flex'); document.body.classList.add('overflow-hidden');
    }
    window.closeVendorCatLb = function(){
        lb.classList.add('hidden'); lb.classList.remove('flex'); document.body.classList.remove('overflow-hidden');
    };
    document.querySelectorAll('.cat-card').forEach(btn => {
        btn.addEventListener('click', () => openLb(btn.dataset.img, btn.dataset.name));
    });
    lb.addEventListener('click', (e) => { if(e.target === lb) closeVendorCatLb(); });
    document.addEventListener('keydown', (e) => { if(e.key === 'Escape' && !lb.classList.contains('hidden')) closeVendorCatLb(); });
})();
</script>
@endsection
