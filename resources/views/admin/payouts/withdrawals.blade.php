@extends('layouts.admin')

@section('title', 'Withdrawal Requests')

@section('content')
<div class="container-fluid py-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h2 class="mb-0">Vendor Withdrawal Requests</h2>
 <span class="badge bg-warning text-dark fs-6">{{ $pendingCount }} pending</span>
 </div>

 @if(session('success'))
 <div class="alert alert-success">{{ session('success') }}</div>
 @endif
 @if(session('error'))
 <div class="alert alert-danger">{{ session('error') }}</div>
 @endif

 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white d-flex justify-content-between align-items-center">
 <h5 class="mb-0">Withdrawals</h5>
 <form method="GET" class="d-flex gap-2">
 <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
 <option value="">All Statuses</option>
 @foreach(['pending','approved','completed','rejected'] as $s)
 <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
 @endforeach
 </select>
 </form>
 </div>
 <div class="card-body p-0">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead class="table-light">
 <tr>
 <th>ID</th>
 <th>Vendor</th>
 <th>Amount</th>
 <th>Method</th>
 <th>Status</th>
 <th>Requested</th>
 <th>Processed</th>
 <th>Actions</th>
 </tr>
 </thead>
 <tbody>
 @forelse($withdrawals as $w)
 <tr>
 <td>{{ $w->id }}</td>
 <td>{{ $w->vendor?->store_name ?? 'N/A' }}</td>
 <td class="fw-bold">₨{{ number_format((float)$w->amount, 2) }}</td>
 <td>{{ ucfirst(str_replace('_', ' ', $w->payout_method)) }}</td>
 <td>
 @switch($w->status)
 @case('pending') <span class="badge bg-warning text-dark">Pending</span> @break
 @case('completed') <span class="badge bg-success">Completed</span> @break
 @case('rejected') <span class="badge bg-danger">Rejected</span> @break
 @default <span class="badge bg-secondary">{{ ucfirst($w->status) }}</span>
 @endswitch
 </td>
 <td>{{ $w->created_at->format('M d, Y H:i') }}</td>
 <td>{{ $w->processed_at?->format('M d, Y H:i') ?? '-' }}</td>
 <td>
 @if($w->status === 'pending')
 <form method="POST" action="{{ route('admin.payouts.withdrawals.approve', $w) }}" class="d-inline">
 @csrf
 <button class="btn btn-sm btn-success me-1" onclick="return confirm('Approve this withdrawal?')">Approve</button>
 </form>
 <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $w->id }}">Reject</button>
 <div class="modal fade" id="rejectModal{{ $w->id }}" tabindex="-1">
 <div class="modal-dialog">
 <form method="POST" action="{{ route('admin.payouts.withdrawals.reject', $w) }}">
 @csrf
 <div class="modal-content">
 <div class="modal-header"><h5 class="modal-title">Reject Withdrawal #{{ $w->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
 <div class="modal-body">
 <label class="form-label">Reason</label>
 <textarea name="reason" class="form-control" rows="3" required></textarea>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
 <button type="submit" class="btn btn-danger">Reject</button>
 </div>
 </div>
 </form>
 </div>
 </div>
 @elseif($w->status === 'rejected')
 <small class="text-muted">{{ Str::limit($w->rejection_reason, 40) }}</small>
 @endif
 </td>
 </tr>
 @empty
 <tr><td colspan="8" class="text-center text-muted py-4">No withdrawal requests found.</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 <div class="card-footer bg-white">
 {{ $withdrawals->links() }}
 </div>
 </div>
</div>
@endsection