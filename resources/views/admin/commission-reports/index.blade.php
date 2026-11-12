@extends('layouts.admin')

@section('title', 'Commission Reports')

@section('content')
<div class="container-fluid py-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h2 class="mb-0">Commission Reports</h2>
 <a href="{{ route('admin.commission.export', request()->query()) }}" class="btn btn-outline-secondary">Export Excel</a>
 </div>

 @if(session('success'))
 <div class="alert alert-success">{{ session('success') }}</div>
 @endif
 @if(session('error'))
 <div class="alert alert-danger">{{ session('error') }}</div>
 @endif

 <div class="row g-3 mb-4">
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="text-muted small">Lifetime Commission</div>
 <div class="fs-4 fw-bold text-primary">₨{{ number_format($report['totals']['commission'], 2) }}</div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="text-muted small">This Month</div>
 <div class="fs-4 fw-bold text-success">₨{{ number_format($report['this_month']['commission'], 2) }}</div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="text-muted small">Paid Out</div>
 <div class="fs-4 fw-bold text-info">₨{{ number_format($report['totals']['paid'], 2) }}</div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="text-muted small">Pending Release</div>
 <div class="fs-4 fw-bold text-warning">₨{{ number_format($report['totals']['pending'], 2) }}</div>
 </div>
 </div>
 </div>
 </div>

 <div class="row g-4 mb-4">
 <div class="col-md-6">
 <div class="card border-0 shadow-sm h-100">
 <div class="card-header bg-white"><h5 class="mb-0">Commission by Category</h5></div>
 <div class="card-body p-0">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead class="table-light">
 <tr><th>Category</th><th>Gross</th><th>Commission</th><th>Orders</th></tr>
 </thead>
 <tbody>
 @forelse($report['by_category'] as $cat)
 <tr>
 <td>{{ $cat['category'] }}</td>
 <td>₨{{ number_format($cat['gross'], 2) }}</td>
 <td class="fw-bold">₨{{ number_format($cat['commission'], 2) }}</td>
 <td>{{ $cat['orders'] }}</td>
 </tr>
 @empty
 <tr><td colspan="4" class="text-center text-muted py-3">No data</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-6">
 <div class="card border-0 shadow-sm h-100">
 <div class="card-header bg-white"><h5 class="mb-0">Commission by Vendor</h5></div>
 <div class="card-body p-0">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead class="table-light">
 <tr><th>Vendor</th><th>Gross</th><th>Commission</th><th>Payouts</th><th>Orders</th></tr>
 </thead>
 <tbody>
 @forelse($report['by_vendor'] as $v)
 <tr>
 <td>{{ $v['vendor'] }}</td>
 <td>₨{{ number_format($v['gross'], 2) }}</td>
 <td class="fw-bold">₨{{ number_format($v['commission'], 2) }}</td>
 <td>₨{{ number_format($v['payouts'], 2) }}</td>
 <td>{{ $v['orders'] }}</td>
 </tr>
 @empty
 <tr><td colspan="5" class="text-center text-muted py-3">No data</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white d-flex justify-content-between align-items-center">
 <h5 class="mb-0">Commission Records</h5>
 <form method="GET" class="d-flex gap-2 flex-wrap">
 <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
 <option value="">All Statuses</option>
 @foreach(['pending','released','paid'] as $s)
 <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
 @endforeach
 </select>
 <select name="vendor_id" class="form-select form-select-sm" onchange="this.form.submit()">
 <option value="">All Vendors</option>
 @foreach($vendors as $v)
 <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->store_name }}</option>
 @endforeach
 </select>
 <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From">
 <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To">
 <button type="submit" class="btn btn-sm btn-primary">Filter</button>
 </form>
 </div>
 <div class="card-body p-0">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead class="table-light">
 <tr>
 <th>ID</th>
 <th>Order</th>
 <th>Vendor</th>
 <th>Gross</th>
 <th>Rate</th>
 <th>Commission</th>
 <th>Payout</th>
 <th>Status</th>
 <th>Date</th>
 <th>Actions</th>
 </tr>
 </thead>
 <tbody>
 @forelse($commissions as $c)
 <tr>
 <td>{{ $c->id }}</td>
 <td>{{ $c->vendorOrder?->order?->order_number ?? '-' }}</td>
 <td>{{ $c->vendorOrder?->vendor?->store_name ?? 'N/A' }}</td>
 <td>₨{{ number_format((float)$c->order_amount, 2) }}</td>
 <td>{{ number_format((float)$c->commission_rate, 2) }}%</td>
 <td class="fw-bold">₨{{ number_format((float)$c->commission_amount, 2) }}</td>
 <td>₨{{ number_format((float)$c->vendor_payout, 2) }}</td>
 <td>
 @switch($c->status)
 @case('pending') <span class="badge bg-warning text-dark">Pending</span> @break
 @case('released') <span class="badge bg-info">Released</span> @break
 @case('paid') <span class="badge bg-success">Paid</span> @break
 @default <span class="badge bg-secondary">{{ ucfirst($c->status) }}</span>
 @endswitch
 </td>
 <td>{{ $c->created_at->format('M d, Y') }}</td>
 <td>
 <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $c->id }}">Adjust</button>
 <div class="modal fade" id="adjustModal{{ $c->id }}" tabindex="-1">
 <div class="modal-dialog">
 <form method="POST" action="{{ route('admin.commission.adjust', $c) }}">
 @csrf
 <div class="modal-content">
 <div class="modal-header"><h5 class="modal-title">Adjust Commission #{{ $c->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
 <div class="modal-body">
 <p class="text-muted small">Current: ₨{{ number_format((float)$c->commission_amount, 2) }} ({{ number_format((float)$c->commission_rate, 2) }}%)</p>
 <div class="mb-3">
 <label class="form-label">New Commission Amount (PKR)</label>
 <input type="number" name="new_amount" class="form-control" step="0.01" min="0" max="{{ $c->order_amount }}" value="{{ $c->commission_amount }}" required>
 </div>
 <div class="mb-3">
 <label class="form-label">Reason</label>
 <textarea name="reason" class="form-control" rows="2" required></textarea>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
 <button type="submit" class="btn btn-primary">Save Adjustment</button>
 </div>
 </div>
 </form>
 </div>
 </div>
 </td>
 </tr>
 @empty
 <tr><td colspan="10" class="text-center text-muted py-4">No commission records found.</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 <div class="card-footer bg-white">{{ $commissions->links() }}</div>
 </div>
</div>
@endsection