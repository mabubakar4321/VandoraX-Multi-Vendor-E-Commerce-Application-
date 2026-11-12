@extends('layouts.vendor')

@section('title', 'Edit Category — VandoraX')

@section('content')
<div class="mb-8">
    <a href="{{ route('vendor.categories.my') }}" class="text-sm font-medium text-forest-500 hover:text-forest">← Back to My Categories</a>
    <h1 class="mt-2 text-[28px] leading-tight">Edit Category</h1>
    <p class="mt-1 text-sm text-graphite">Updating <span class="font-semibold text-forest">{{ $category->name }}</span> — {{ $vendor->store_name }}</p>
</div>

@if ($errors->any())
    <div class="mb-6 border border-vermilion/30 bg-vermilion-50 px-4 py-3 text-sm text-vermilion" style="border-radius: 2px;">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('vendor.categories.update', $category) }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
    @csrf
    @method('PUT')

    <div class="border border-mist bg-white p-6 space-y-6" style="border-radius: 2px;">
        <div>
            <label for="name" class="block text-sm font-semibold text-forest">Category Name <span class="text-vermilion">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="255"
                   class="mt-2 w-full border border-mist bg-white px-3 py-2.5 text-sm focus:border-forest focus:outline-none" placeholder="e.g. Smart Gadgets">
            @error('name')<p class="mt-1 text-xs text-vermilion">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-forest">Description</label>
            <textarea id="description" name="description" rows="3" maxlength="1000"
                      class="mt-2 w-full border border-mist bg-white px-3 py-2.5 text-sm focus:border-forest focus:outline-none" placeholder="Brief description...">{{ old('description', $category->description) }}</textarea>
            @error('description')<p class="mt-1 text-xs text-vermilion">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-forest">Current Picture</label>
            <div class="mt-3">
                @if($category->icon)
                    <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="h-32 w-48 object-cover border border-mist" style="border-radius: 2px;">
                    <p class="mt-2 text-xs text-graphite/60">{{ $category->icon }}</p>
                @else
                    <p class="text-sm text-graphite/60">No picture yet.</p>
                @endif
            </div>
        </div>

        <div>
            <label for="icon" class="block text-sm font-semibold text-forest">New Picture <span class="font-normal text-graphite/60">(optional — jpg, png, webp, max 2MB)</span></label>
            <input type="file" id="icon" name="icon" accept="image/jpeg,image/png,image/webp"
                   class="mt-2 w-full text-sm file:mr-4 file:rounded-none file:border-0 file:bg-forest file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-forest/90">
            @error('icon')<p class="mt-1 text-xs text-vermilion">{{ $message }}</p>@enderror
            <div id="previewWrap" class="mt-3 hidden">
                <p class="text-xs font-medium text-forest mb-1">Preview:</p>
                <img id="previewImg" src="" alt="Preview" class="h-32 w-48 object-cover border border-mist" style="border-radius: 2px;">
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="bg-forest px-6 py-2.5 text-sm font-semibold text-white hover:bg-forest/90">Update Category</button>
        <a href="{{ route('vendor.categories.my') }}" class="border border-mist bg-white px-6 py-2.5 text-sm font-semibold text-forest hover:bg-paper">Cancel</a>
    </div>
</form>

<script>
(()=>{const i=document.getElementById('icon'),w=document.getElementById('previewWrap'),p=document.getElementById('previewImg'); if(i) i.addEventListener('change',()=>{const f=i.files[0]; if(!f){w.classList.add('hidden');return;} p.src=URL.createObjectURL(f); w.classList.remove('hidden');});})();
</script>
@endsection
