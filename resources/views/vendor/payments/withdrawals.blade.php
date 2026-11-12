@extends('layouts.vendor')

@section('title', 'Withdrawals')

@section('content')
<div class="container-fluid py-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-0">Withdrawal Requests</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawModal">Request Withdrawal</button>
 </div>

 @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
 @endif
 @if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
 @endif

 <div class="row g-3 mb-4">
  <div class="col-md-4">
   <div class="card border-0 shadow-sm">
    <div class="card-body">
     <div class="text-muted small">Available for Withdrawal</div>
     <div class="fs-4 fw-bold text-success">₨{{ number_format($pendingPayout['pending_net'], 2) }}</div>
     <small class="text-muted">Min threshold: ₨{{ number_format($pendingPayout['min_threshold'], 0) }}</small>
    </div>
   </div>
  </div>
  <div class="col-md-4">
   <div class="card border-0 shadow-sm">
    <div class="card-body">
     <div class="text-muted small">Eligible Orders</div>
     <div class="fs-4 fw-bold">{{ $pendingPayout['eligible_orders'] }}</div>
    </div>
   </div>
  </div>
  <div class="col-md-4">
   <div class="card border-0 shadow-sm">
    <div class="card-body">
     <div class="text-muted small">Status</div>
     @if($pendingPayout['held_due_to_dispute'])
      <div class="fs-5 fw-bold text-danger">Held (Dispute)</div>
     @elseif($pendingPayout['meets_threshold'])
      <div class="fs-5 fw-bold text-success">Ready to Withdraw</div>
     @else
      <div class="fs-5 fw-bold text-warning">Below Threshold</div>
     @endif
    </div>
   </div>
  </div>
 </div>

 <div class="card border-0 shadow-sm">
  <div class="card-header bg-white"><h5 class="mb-0">Withdrawal History</h5></div>
  <div class="card-body p-0">
   <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
     <thead class="table-light">
      <tr>
       <th>ID</th>
       <th>Amount</th>
       <th>Method</th>
       <th>Status</th>
       <th>Requested</th>
       <th>Processed</th>
       <th>Notes</th>
      </tr>
     </thead>
     <tbody>
      @forelse($withdrawals as $w)
       <tr>
        <td>{{ $w->id }}</td>
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
         @if($w->status === 'rejected')
          <small class="text-danger">{{ Str::limit($w->rejection_reason, 50) }}</small>
         @else
          <small class="text-muted">{{ Str::limit($w->notes, 50) }}</small>
         @endif
        </td>
       </tr>
      @empty
       <tr><td colspan="7" class="text-center text-muted py-4">No withdrawal requests yet.</td></tr>
      @endforelse
     </tbody>
    </table>
   </div>
  </div>
  <div class="card-footer bg-white">{{ $withdrawals->links() }}</div>
 </div>
</div>

<div class="modal fade" id="withdrawModal" tabindex="-1">
 <div class="modal-dialog">
  <form method="POST" action="{{ route('vendor.payments.withdraw.request') }}">
   @csrf
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title">Request Withdrawal</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
     <p class="text-muted small">Available: ₨{{ number_format($pendingPayout['pending_net'], 2) }} · Min: ₨{{ number_format($pendingPayout['min_threshold'], 0) }}</p>
     <div class="mb-3">
      <label class="form-label">Amount (PKR)</label>
      <input type="number" name="amount" class="form-control" step="0.01" min="{{ $pendingPayout['min_threshold'] }}" max="{{ $pendingPayout['pending_net'] }}" required>
     </div>
     <div class="mb-3">
      <label class="form-label">Payout Method</label>
      <select name="payout_method" class="form-select">
       <option value="bank_transfer">Bank Transfer</option>
       <option value="check">Check</option>
       <option value="wallet">Wallet</option>
      </select>
     </div>
     <div class="mb-3">
      <label class="form-label">Notes (optional)</label>
      <textarea name="notes" class="form-control" rows="2" maxlength="500"></textarea>
     </div>
    </div>
    <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
     <button type="submit" class="btn btn-primary">Submit Request</button>
    </div>
   </div>
  </form>
 </div>
</div>
@endsection