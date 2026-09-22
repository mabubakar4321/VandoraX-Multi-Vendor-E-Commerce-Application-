@extends('layouts.admin')

@section('title', 'Vendor Detail')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.vendors.pending') }}" class="text-indigo-600 hover:text-indigo-700 flex items-center mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to List
            </a>
            <h1 class="text-4xl font-bold text-gray-900">Vendor Application Review</h1>
            <p class="text-gray-600 mt-2">{{ $vendor->user->name }} - {{ $vendor->store_name }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Status</p>
            @if($vendor->status === 'pending_documents')
                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-medium">📄 Pending Documents</span>
            @elseif($vendor->status === 'under_review')
                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full font-medium">🔍 Under Review</span>
            @elseif($vendor->status === 'active')
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-medium">✓ Active</span>
            @elseif($vendor->status === 'rejected')
                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-medium">✗ Rejected</span>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Personal Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Full Name</p>
                        <p class="text-lg font-medium text-gray-900">{{ $vendor->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email Address</p>
                        <p class="text-lg font-medium text-gray-900">{{ $vendor->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone Number</p>
                        <p class="text-lg font-medium text-gray-900">{{ $vendor->user->phone_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email Verified</p>
                        <p class="text-lg font-medium">
                            @if($vendor->user->email_verified_at)
                                <span class="text-green-600">✓ Yes</span>
                            @else
                                <span class="text-red-600">✗ No</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Store Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Store Information</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Store Name</p>
                        <p class="text-lg font-medium text-gray-900">{{ $vendor->store_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Store Slug</p>
                        <p class="text-lg font-medium text-gray-900 font-mono">{{ $vendor->store_slug ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="text-lg font-medium text-gray-900">{{ $vendor->category->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Store Description</p>
                        <p class="text-gray-900">{{ $vendor->store_description ?? 'No description provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            @if($bankDetails)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Bank Details</h2>
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4 flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm">Bank details are encrypted at rest and shown here only to authorized administrators.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Account Holder Name</p>
                            <p class="text-lg font-medium text-gray-900">{{ $bankDetails['account_holder_name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Bank Name</p>
                            <p class="text-lg font-medium text-gray-900">{{ $bankDetails['bank_name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Account Type</p>
                            <p class="text-lg font-medium text-gray-900">{{ ucfirst($bankDetails['account_type'] ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Account Number</p>
                            <p class="text-lg font-medium text-gray-900">{{ $bankDetails['account_number'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">IBAN</p>
                            <p class="text-lg font-medium text-gray-900">{{ $bankDetails['iban'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documents -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Submitted Documents</h2>
                <div class="space-y-3">
                    @if($documents->count() > 0)
                        @foreach($documents as $doc)
                            <div class="border rounded-lg p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        @if(in_array($doc->document_type, ['cnic', 'bank_proof']))
                                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">
                                            @if($doc->document_type === 'cnic')
                                                CNIC/ID
                                            @elseif($doc->document_type === 'tax_certificate')
                                                Tax Certificate
                                            @elseif($doc->document_type === 'bank_proof')
                                                Bank Proof
                                            @else
                                                {{ ucfirst($doc->document_type) }}
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $doc->file_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($doc->status === 'approved')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">✓ Approved</span>
                                    @elseif($doc->status === 'rejected')
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">✗ Rejected</span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">⏳ Pending</span>
                                    @endif
                                    <a href="{{ route('admin.vendors.document.view', $doc->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                        View
                                    </a>
                                    @if($doc->status === 'pending')
                                        <form action="{{ route('admin.vendors.document.approve', $doc->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-700 font-medium text-sm">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.vendors.document.reject', $doc->id) }}" method="POST"
                                              onsubmit="return rejectDocument(this);">
                                            @csrf
                                            <input type="hidden" name="reason">
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm">
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-600">
                            <p>No documents uploaded yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Timeline</h2>
                <div class="space-y-4">
                    <div class="flex">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Registered</p>
                            <p class="text-sm text-gray-600">{{ $vendor->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                    @if($vendor->verification_date)
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Approved</p>
                                <p class="text-sm text-gray-600">{{ $vendor->verification_date->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1">
            <!-- Approval Actions -->
            @if($vendor->status !== 'active' && $vendor->status !== 'rejected')
                <div class="bg-white rounded-lg shadow p-6 mb-6 sticky top-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>

                    <!-- Approve Button -->
                    <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Approve Vendor
                        </button>
                    </form>

                    <!-- Reject Button -->
                    <button type="button" onclick="openRejectModal()" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Reject Vendor
                    </button>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Status</h3>
                    @if($vendor->status === 'active')
                        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start">
                            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="font-medium">Approved</p>
                                <p class="text-sm">This vendor is approved and can list products</p>
                            </div>
                        </div>
                    @elseif($vendor->status === 'rejected')
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start">
                            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="font-medium">Rejected</p>
                                <p class="text-sm">{{ $vendor->rejection_reason ?? 'No reason provided' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Document Status -->
            @if($documentStatus)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Document Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Total Documents</span>
                            <span class="font-bold text-gray-900">{{ $documentStatus['total'] }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Approved</span>
                            <span class="font-bold text-green-600">{{ $documentStatus['approved'] }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Pending</span>
                            <span class="font-bold text-yellow-600">{{ $documentStatus['pending'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Rejected</span>
                            <span class="font-bold text-red-600">{{ $documentStatus['rejected'] }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-900">Reject Vendor Application</h2>
                <p class="text-gray-600 mt-1">Please provide a reason for rejection</p>
            </div>
            <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                    <textarea name="reason" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Please explain why you're rejecting this application..."></textarea>
                    @error('reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function rejectDocument(form) {
            const reason = window.prompt('Reason for rejecting this document:');

            if (!reason || reason.trim().length < 5) {
                return false;
            }

            form.elements.reason.value = reason.trim();
            return true;
        }

        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
</div>
@endsection