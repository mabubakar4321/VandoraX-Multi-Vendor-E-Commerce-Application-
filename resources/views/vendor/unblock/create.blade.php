@extends('layouts.vendor')

@section('title', 'Request Unblock')

@section('content')
<div class="w-full px-4 sm:px-6 py-6 sm:py-10 max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-8">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Request Store Unblock</h1>
        <p class="text-gray-600 mb-6">Your store has been blocked due to negative reviews. Submit this form to request reinstatement. Our team will review your request and respond within 2–3 business days.</p>

        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3 min-w-0">
                    <h3 class="text-sm font-semibold text-red-800">Store Blocked</h3>
                    <div class="mt-1.5 text-sm text-red-700">
                        <p class="leading-relaxed">{{ $vendor->unblock_message ?? 'Multiple negative reviews received.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('vendor.unblock.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label for="improvements" class="block text-sm font-medium text-gray-700 mb-2">What improvements have you made? <span class="text-red-500">*</span></label>
                <textarea name="improvements" id="improvements" rows="5" required minlength="50" maxlength="5000"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Describe specific changes to product quality, shipping, customer service, etc.">{{ old('improvements') }}</textarea>
                @error('improvements')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="reason_to_unblock" class="block text-sm font-medium text-gray-700 mb-2">Why should we unblock your store? <span class="text-red-500">*</span></label>
                <textarea name="reason_to_unblock" id="reason_to_unblock" rows="5" required minlength="50" maxlength="5000"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Explain why customers will have a better experience going forward.">{{ old('reason_to_unblock') }}</textarea>
                @error('reason_to_unblock')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Supporting Evidence (optional)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-emerald-400 transition-colors">
                    <input type="file" name="evidence_files[]" id="evidence-files" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                    <label for="evidence-files" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span class="text-sm text-gray-600">Upload certificates, improvement plans, or other evidence</span>
                        <span class="block text-xs text-gray-400 mt-1">PDF, JPG, PNG, DOC up to 5MB each (max 5 files)</span>
                    </label>
                </div>
                <div id="evidence-preview" class="flex gap-2 mt-3 flex-wrap text-sm text-gray-600"></div>
                @error('evidence_files.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 sm:gap-4">
                <a href="{{ route('vendor.dashboard') }}" class="w-full sm:w-auto px-4 py-2.5 text-center text-sm font-medium text-gray-700 hover:text-gray-900 border border-gray-300 rounded-md">Cancel</a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 text-center border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('evidence-files').addEventListener('change', function(e) {
    const container = document.getElementById('evidence-preview');
    container.innerHTML = '';
    Array.from(e.target.files).slice(0, 5).forEach(file => {
        const span = document.createElement('span');
        span.className = 'inline-flex items-center px-2 py-1 rounded bg-gray-100 text-xs';
        span.textContent = file.name;
        container.appendChild(span);
    });
});
</script>
@endpush
@endsection