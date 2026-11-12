@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Catalog management</p>
            <h1 class="mt-2 text-3xl leading-tight">Categories</h1>
            <p class="mt-2 text-sm text-graphite/70">Organize products, category hierarchy, and commission rates.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary w-full px-5 py-2.5 sm:w-auto">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Category
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="border-t-2 border-forest-500 bg-white p-4 shadow-sm" style="border-radius: 2px;">
            <div class="flex items-center">
                <div class="rounded-full bg-forest-100 p-3">
                    <svg class="h-6 w-6 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a2 2 0 012-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-graphite/70">Total categories</p>
                    <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="border-t-2 border-marigold bg-white p-4 shadow-sm" style="border-radius: 2px;">
            <div class="flex items-center">
                <div class="rounded-full bg-marigold-100 p-3">
                    <svg class="h-6 w-6 text-marigold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-graphite/70">Active</p>
                    <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['active']) }}</p>
                </div>
            </div>
        </div>

        <div class="border-t-2 border-indigo-500 bg-white p-4 shadow-sm" style="border-radius: 2px;">
            <div class="flex items-center">
                <div class="rounded-full bg-forest-100 p-3">
                    <svg class="h-6 w-6 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-graphite/70">Main categories</p>
                    <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['main']) }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.categories.tree') }}" class="block border-t-2 border-vermilion bg-white p-4 shadow-sm transition-shadow hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-marigold" style="border-radius: 2px;">
            <div class="flex items-center">
                <div class="rounded-full bg-vermilion-100 p-3">
                    <svg class="h-6 w-6 text-vermilion" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-graphite/70">Subcategories</p>
                    <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['sub']) }}</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="border border-mist bg-white p-4 sm:p-5" style="border-radius: 2px;">
        <form method="GET" class="grid min-w-0 gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_auto_auto_auto]">
            <div class="min-w-0">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search categories..."
                    class="field" aria-label="Search categories">
            </div>
            <button type="submit" class="btn-primary w-full px-5 py-2.5 xl:w-auto">
                Search
            </button>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center border border-mist px-5 py-2.5 text-center text-sm font-semibold text-forest transition-colors hover:bg-paper">
                Reset
            </a>
            <a href="{{ route('admin.categories.tree') }}" class="btn-dark w-full px-5 py-2.5 xl:w-auto">
                Tree View
            </a>
        </form>
    </div>

    <!-- Categories Table -->
    <section class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
        <div class="border-b border-mist px-5 py-4 sm:px-6"><h2 class="text-xl">Category directory</h2><p class="mt-1 text-sm text-graphite/65">Review category structure, commission, and availability.</p></div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65">
                <tr>
                    <th class="px-5 py-3 sm:px-6">Category</th>
                    <th class="px-5 py-3 sm:px-6">Type</th>
                    <th class="px-5 py-3 sm:px-6">Commission</th>
                    <th class="px-5 py-3 sm:px-6">Products</th>
                    <th class="px-5 py-3 sm:px-6">Status</th>
                    <th class="px-5 py-3 text-right sm:px-6">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-mist">
                @forelse($categories as $category)
                <tr class="hover:bg-paper/70">
                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex min-w-0 items-center">
                            @if($category->icon)
                                <button type="button" class="mr-3 shrink-0 cat-thumb-btn" data-img="{{ asset('storage/' . $category->icon) }}" data-name="{{ $category->name }}" aria-label="View {{ $category->name }} image">
                                    <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="h-9 w-9 rounded-full object-cover ring-1 ring-mist hover:ring-forest transition" loading="lazy" onerror="this.style.display='none'">
                                </button>
                            @else
                                <div class="mr-3 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a2 2 0 012-2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-forest">{{ $category->name }}</p>
                                <p class="mt-1 break-all text-xs text-graphite/60">{{ $category->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->isMainCategory() ? 'bg-forest-100 text-forest' : 'bg-marigold-100 text-forest' }}">
                            {{ $category->isMainCategory() ? 'Main' : 'Sub' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 font-semibold text-forest sm:px-6">
                        {{ $category->commission_rate }}%
                    </td>
                    <td class="px-5 py-4 text-graphite sm:px-6">
                        {{ number_format($category->products()->count()) }}
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-forest-100 text-forest' : 'bg-vermilion-100 text-vermilion' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right sm:px-6">
                        <div class="flex justify-end gap-4">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline delete-category-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="font-semibold text-vermilion hover:text-vermilion/80 delete-category-button"
                                    data-category-name="{{ $category->name }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="font-semibold text-forest">No categories found</p>
                        <p class="mt-1 text-sm text-graphite/65">Try a different search or create a category to build your catalog.</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn-primary mx-auto mt-5 w-auto px-5 py-2.5">Add category</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </section>

    <!-- Pagination -->
    @if(method_exists($categories, 'links'))
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
    @endif
</div>

<div id="delete-category-modal" class="fixed inset-0 z-50 hidden overflow-y-auto p-4" aria-labelledby="delete-category-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-forest/60 backdrop-blur-sm" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center">
        <div class="w-full max-w-md overflow-hidden border border-mist bg-white shadow-2xl" style="border-radius: 4px;">
            <div class="flex items-start gap-4 border-b border-mist px-6 py-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-vermilion-100 text-vermilion" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14"></path>
                    </svg>
                </div>
                <div>
                    <h2 id="delete-category-title" class="text-xl font-semibold text-forest">Delete category?</h2>
                    <p class="mt-1 text-sm leading-relaxed text-graphite">You are about to remove <strong id="delete-category-name" class="font-semibold text-forest"></strong>. This action cannot be undone.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 bg-paper px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" class="border border-mist bg-white px-5 py-2.5 text-sm font-semibold text-forest transition-colors hover:bg-mist" data-modal-close>
                    Keep category
                </button>
                <button type="button" id="confirm-delete-category" class="bg-vermilion px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-vermilion/90">
                    Delete category
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('delete-category-modal');
        const categoryName = document.getElementById('delete-category-name');
        const confirmButton = document.getElementById('confirm-delete-category');
        let selectedForm = null;

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            selectedForm = null;
        };

        document.querySelectorAll('.delete-category-button').forEach((button) => {
            button.addEventListener('click', () => {
                selectedForm = button.closest('.delete-category-form');
                categoryName.textContent = button.dataset.categoryName;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                confirmButton.focus();
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        confirmButton.addEventListener('click', () => {
            if (selectedForm) {
                selectedForm.submit();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
<div id="adminCatLightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-forest/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Category image">
    <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25" aria-label="Close" onclick="document.getElementById('adminCatLightbox').classList.add('hidden'); document.getElementById('adminCatLightbox').classList.remove('flex'); document.body.classList.remove('overflow-hidden')">
        <span class="text-2xl leading-none">×</span>
    </button>
    <img id="adminCatLightboxImg" src="" alt="" class="max-h-[86vh] max-w-[92vw] rounded-lg bg-white shadow-2xl">
    <p id="adminCatLightboxCaption" class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/40 px-4 py-1.5 text-sm font-semibold text-white"></p>
</div>
<script>
    (() => {
        const lb = document.getElementById('adminCatLightbox');
        const lbImg = document.getElementById('adminCatLightboxImg');
        const lbCap = document.getElementById('adminCatLightboxCaption');
        function openLb(src, name){
            lbImg.src = src; lbImg.alt = name || 'Category image'; lbCap.textContent = name || '';
            lb.classList.remove('hidden'); lb.classList.add('flex'); document.body.classList.add('overflow-hidden');
        }
        function closeLb(){ lb.classList.add('hidden'); lb.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); }
        document.querySelectorAll('.cat-thumb-btn').forEach(btn => {
            btn.addEventListener('click', () => openLb(btn.dataset.img, btn.dataset.name));
        });
        lb.addEventListener('click', (e) => { if(e.target === lb) closeLb(); });
        document.addEventListener('keydown', (e) => { if(e.key === 'Escape' && !lb.classList.contains('hidden')) closeLb(); });
    })();
</script>
@endsection
