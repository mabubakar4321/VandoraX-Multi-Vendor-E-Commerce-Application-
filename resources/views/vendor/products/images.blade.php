@extends('layouts.vendor')

@section('title', 'Manage Images - ' . $product->name)

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('vendor.products.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Products
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Manage Images</h1>
        <p class="text-gray-600 mt-1">{{ $product->name }}</p>
    </div>

    <!-- Image Upload Section -->
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Upload Images</h2>

        <form id="uploadForm" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Drag & Drop Zone -->
            <div id="dropZone" class="border-2 border-dashed border-indigo-300 rounded-lg p-12 text-center cursor-pointer hover:bg-indigo-50 transition">
                <svg class="mx-auto h-12 w-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <p class="mt-2 text-sm font-medium text-gray-900">Drag and drop images here</p>
                <p class="text-xs text-gray-500 mt-1">or click to select (3-10 images, max 5MB each)</p>
                <input type="file" id="fileInput" name="images[]" multiple accept="image/*" style="display: none;">
            </div>

            <!-- Progress Bar -->
            <div id="progressContainer" style="display: none;">
                <p class="text-sm font-medium text-gray-700 mb-2">Uploading...</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-indigo-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" style="display: none;" class="bg-red-50 border-l-4 border-red-600 p-4 rounded">
                <p id="errorText" class="text-red-600"></p>
            </div>

            <!-- Upload Button -->
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                Upload Images
            </button>
        </form>
    </div>

    <!-- Current Images Gallery -->
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Current Images</h2>

        @if($product->images->count() > 0)
            <div id="galleryContainer" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($product->images as $image)
                <div class="relative group" id="image-{{ $image->id }}">
                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="{{ $image->alt_text }}" class="w-full h-64 object-cover rounded-lg">
                    
                    <!-- Featured Badge -->
                    @if($image->is_featured)
                    <span class="absolute top-2 left-2 bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        Featured
                    </span>
                    @endif

                    <!-- Overlay Actions -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition rounded-lg flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100">
                        <button onclick="setFeatured({{ $image->id }})" class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </button>
                        <button type="button" class="delete-image-btn bg-red-600 hover:bg-red-700 text-white p-2 rounded-full"
                                data-image-id="{{ $image->id }}"
                                data-image-alt="{{ $image->alt_text ?: 'Image ' . $image->id }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Info -->
                    <div class="mt-2">
                        <input type="text" placeholder="Alt text..." value="{{ $image->alt_text }}" 
                            onchange="updateAltText({{ $image->id }}, this.value)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded">
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-8">No images uploaded yet. Upload your first image above.</p>
        @endif
    </div>
</div>

<script>
// Drag and drop
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

dropZone.addEventListener('click', () => fileInput.click());

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.add('border-indigo-500', 'bg-indigo-50'));
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-indigo-500', 'bg-indigo-50'));
});

dropZone.addEventListener('drop', (e) => {
    fileInput.files = e.dataTransfer.files;
});

// Form submission
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const files = fileInput.files;
    if (files.length === 0) {
        showError('Please select at least one image');
        return;
    }

    if (files.length > 10) {
        showError('Maximum 10 images allowed');
        return;
    }

    const formData = new FormData(this);
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    
    progressContainer.style.display = 'block';

    try {
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', (e) => {
            const percent = (e.loaded / e.total) * 100;
            progressBar.style.width = percent + '%';
        });

        xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
                location.reload();
            } else {
                showError('Upload failed');
            }
        });

        xhr.addEventListener('error', () => {
            showError('Network error');
        });

        xhr.open('POST', '{{ route("vendor.products.images.upload", $product) }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.send(formData);
    } catch (error) {
        showError(error.message);
    }
});

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    document.getElementById('errorText').textContent = message;
    errorDiv.style.display = 'block';
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

function setFeatured(imageId) {
    fetch(`/vendor/products/{{ $product->id }}/images/${imageId}/featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(() => location.reload());
}

function deleteImage(imageId) {
    if (confirm('Delete this image?')) {
        fetch(`/vendor/products/{{ $product->id }}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => location.reload());
    }
}

function updateAltText(imageId, altText) {
    fetch(`/vendor/products/{{ $product->id }}/images/${imageId}/alt-text`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ alt_text: altText })
    });
}

// Delete image confirmation modal
const deleteImageModal = document.getElementById('delete-image-modal');
const deleteImageName = document.getElementById('delete-image-name');
const deleteImageConfirmBtn = document.getElementById('delete-image-confirm');
const deleteImageCancelBtn = document.getElementById('delete-image-cancel');
let currentDeleteImageId = null;

const closeDeleteImageModal = () => {
    deleteImageModal.classList.add('hidden');
    deleteImageModal.classList.remove('flex');
    currentDeleteImageId = null;
};

document.querySelectorAll('.delete-image-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        const imageId = btn.dataset.imageId;
        const imageAlt = btn.dataset.imageAlt;

        deleteImageName.textContent = imageAlt;
        currentDeleteImageId = imageId;

        deleteImageModal.classList.remove('hidden');
        deleteImageModal.classList.add('flex');
        deleteImageConfirmBtn.focus();
    });
});

deleteImageConfirmBtn.addEventListener('click', () => {
    if (currentDeleteImageId) {
        fetch(`/vendor/products/{{ $product->id }}/images/${currentDeleteImageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => location.reload());
    }
});

deleteImageCancelBtn.addEventListener('click', closeDeleteImageModal);
deleteImageModal.addEventListener('click', (event) => {
    if (event.target === deleteImageModal) closeDeleteImageModal();
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !deleteImageModal.classList.contains('hidden')) closeDeleteImageModal();
});

</script>

{{-- Delete Image Confirmation Modal --}}
<div id="delete-image-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="delete-image-modal-title">
    <div class="relative w-full max-w-md rounded-lg bg-white shadow-2xl">
        {{-- Modal Header --}}
        <div class="border-b border-gray-200 px-6 py-5">
            <h2 id="delete-image-modal-title" class="text-lg font-semibold text-gray-900">Delete Image</h2>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-6">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <p class="text-[15px] text-gray-700">
                Are you sure you want to delete this image?
                <span id="delete-image-name" class="block font-semibold text-gray-900 mt-2"></span>
                This action cannot be undone.
            </p>
        </div>

        {{-- Modal Footer --}}
        <div class="border-t border-gray-200 flex gap-3 px-6 py-4">
            <button type="button" id="delete-image-cancel"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" id="delete-image-confirm"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors">
                Delete
            </button>
        </div>
    </div>
</div>
@endsection