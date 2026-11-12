@extends('layouts.vendor')

@section('title', 'Unblock Request Status')

@section('content')
<div class="w-full px-4 sm:px-6 py-6 sm:py-10 max-w-3xl mx-auto">
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-8">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
 <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Unblock Request Status</h1>
 @php
 $statusColors = [
 'pending' => 'bg-yellow-100 text-yellow-800',
 'approved' => 'bg-green-100 text-green-800',
 'rejected' => 'bg-red-100 text-red-800',
 ];
 @endphp
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$unblockRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
 {{ ucfirst($unblockRequest->status) }}
 </span>
 </div>

 @if ($unblockRequest->status === 'approved')
 <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
 <p class="text-green-800 font-medium">Your store has been unblocked! You can now add products and accept new orders.</p>
 </div>
 @elseif ($unblockRequest->status === 'rejected')
 <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
 <p class="text-red-800 font-medium">Your request was rejected.</p>
 @if ($unblockRequest->admin_response)
 <p class="text-red-700 mt-2 text-sm">Reason: {{ $unblockRequest->admin_response }}</p>
 @endif
 </div>
 @else
 <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
 <p class="text-yellow-800">Your request is under review. We will notify you once a decision has been made.</p>
 </div>
 @endif

 <dl class="space-y-4">
 <div>
 <dt class="text-sm font-medium text-gray-500">Submitted</dt>
 <dd class="mt-1 text-sm text-gray-900">{{ $unblockRequest->created_at->format('M d, Y \a\t g:i A') }}</dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500">Request Details</dt>
 <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $unblockRequest->reason }}</dd>
 </div>
 @if ($unblockRequest->reviewed_at)
 <div>
 <dt class="text-sm font-medium text-gray-500">Reviewed At</dt>
 <dd class="mt-1 text-sm text-gray-900">{{ $unblockRequest->reviewed_at->format('M d, Y \a\t g:i A') }}</dd>
 </div>
 @endif
 </dl>

 <div class="mt-8 flex justify-end">
  <a href="{{ route('vendor.unblock.index') }}" class="text-sm text-emerald-600 hover:text-emerald-900">&larr; All Requests</a>
 </div>
 <div class="mt-4 sm:hidden">
  <a href="{{ route('vendor.reviews.index') }}" class="inline-block w-full text-center text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white rounded-md px-4 py-2.5">Back to Reviews &amp; Reputation</a>
 </div>
 </div>
</div>
@endsection