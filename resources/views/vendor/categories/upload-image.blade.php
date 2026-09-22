@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('vendor.categories.requests') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Categories
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">
            Upload Category Image
        </h1>
        <p class="text-gray-600 mt-2">
            {{ $categoryRequest->category_name }}
        </p>
    </div>

    <!-- Status Alert -->
    @if (!$categoryRequest->isApproved())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex gap-3">
                <div class="text-yellow-600 text-xl">⚠️</div>
                <div>
                    <h3 class="font-semibold text-yellow-900">Not Approved Yet</h3>
                    <p class="text-yellow-800 text-sm">This category request needs to be approved by admin before you can upload an image.</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex gap-3">
                <div class="text-green-600 text-xl">✅</div>
                <div>
                    <h3 class="font-semibold text-green-900">Approved</h3>
                    <p class="text-green-800 text-sm">This category has been approved. You can now upload an image.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Current Image Preview -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Current Image</h3>

                @if ($categoryRequest->hasImage())
                    <div class="mb-4">
                        <img
                            src="{{ $categoryRequest->getImageUrl() }}"
                            alt="{{ $categoryRequest->image_alt_text }}"
                            class="w-full h-48 object-cover rounded-lg mb-4"
                        >
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><strong>Alt Text:</strong> {{ $categoryRequest->image_alt_text ?? 'Not set' }}</p>
                            <p><strong>Size:</strong> {{ $categoryRequest->image_path ? 'Uploaded' : 'Unknown' }}</p>
                        </div>

                        <!-- Delete Button -->
                        <form action="{{ route('vendor.categories.image.destroy', $categoryRequest) }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                onclick="return confirm('Are you sure you want to delete this image?')"
                            >
                                🗑️ Delete Image
                            </button>
                        </form>
                    </div>
                @else
                    <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                        <div class="text-center">
                            <div class="text-4xl mb-2">📸</div>
                            <p class="text-gray-500 text-sm">No image uploaded yet</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upload Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Upload New Image</h3>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-red-900 mb-2">Errors:</h4>
                        <ul class="text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('vendor.categories.image.store', $categoryRequest) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Image Upload Area -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition cursor-pointer bg-gray-50 hover:bg-blue-50" id="dropZone">
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-4xl mb-3">📤</div>
                            <p class="text-gray-900 font-semibold mb-1">Drag and drop your image here</p>
                            <p class="text-gray-500 text-sm mb-4">or click to browse</p>
                            <p class="text-gray-500 text-xs">
                                Supported formats: JPEG, PNG, GIF, WebP
                                <br>
                                Maximum file size: 2MB
                            </p>
                        </div>
                        <input
                            type="file"
                            name="image"
                            id="imageInput"
                            accept="image/*"
                            class="hidden"
                            {{ !$categoryRequest->isApproved() ? 'disabled' : '' }}
                        >
                    </div>

                    <!-- Image Preview -->
                    <div id="previewContainer" class="hidden">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Preview</label>
                        <img id="previewImage" src="" alt="Preview" class="w-full h-64 object-cover rounded-lg">
                        <p id="fileName" class="text-sm text-gray-600 mt-2"></p>
                        <button
                            type="button"
                            class="mt-2 px-4 py-2 text-gray-600 hover:text-gray-900 text-sm"
                            onclick="clearPreview()"
                        >
                            ✕ Change Image
                        </button>
                    </div>

                    <!-- Alt Text Field -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Alt Text <span class="text-gray-500 text-xs">(optional but recommended)</span>
                        </label>
                        <input
                            type="text"
                            name="alt_text"
                            value="{{ $categoryRequest->image_alt_text }}"
                            placeholder="e.g., {{ $categoryRequest->category_name }} category thumbnail"
                            maxlength="255"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            {{ !$categoryRequest->isApproved() ? 'disabled' : '' }}
                        >
                        <p class="text-gray-500 text-xs mt-1">
                            Alt text helps with accessibility and SEO. Describe what's in the image.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submitBtn"
                            {{ !$categoryRequest->isApproved() ? 'disabled' : '' }}
                        >
                            📤 Upload Image
                        </button>
                        <a
                            href="{{ route('vendor.categories.requests') }}"
                            class="px-6 py-3 bg-gray-200 text-gray-900 font-semibold rounded-lg hover:bg-gray-300 transition"
                        >
                            Cancel
                        </a>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">💡 Image Guidelines</h4>
                    <ul class="text-blue-800 text-sm space-y-1">
                        <li>✓ Use a square image (1:1 aspect ratio) for best results</li>
                        <li>✓ Recommended size: 500x500px or larger</li>
                        <li>✓ Keep file size under 2MB</li>
                        <li>✓ Use high-quality, clear images</li>
                        <li>✓ Avoid watermarks or text overlays</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for drag and drop -->
<script>
const dropZone = document.getElementById('dropZone');
const imageInput = document.getElementById('imageInput');
const previewContainer = document.getElementById('previewContainer');
const previewImage = document.getElementById('previewImage');
const fileName = document.getElementById('fileName');

// Click to select file
dropZone.addEventListener('click', () => imageInput.click());

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-blue-500', 'bg-blue-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-blue-500', 'bg-blue-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-blue-500', 'bg-blue-50');

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        imageInput.files = files;
        previewFile();
    }
});

// File input change
imageInput.addEventListener('change', previewFile);

// Preview file
function previewFile() {
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            fileName.textContent = `📁 ${file.name} (${formatFileSize(file.size)})`;
            dropZone.style.display = 'none';
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function clearPreview() {
    imageInput.value = '';
    previewContainer.classList.add('hidden');
    dropZone.style.display = 'flex';
}

// Format file size
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}
</script>
@endsection
