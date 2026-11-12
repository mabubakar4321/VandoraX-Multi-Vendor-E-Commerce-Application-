@extends('layouts.admin')

@section('title', 'Vendor Payouts')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Vendor Payouts</h2>
        <div>
            <form method="POST" action="{{ route('admin.payouts.process') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary me-2" onclick="return confirm('Run batch payout processing now?')">Process Payouts Now</button>
            </form>
            <a href="{{ route('admin.payouts.export') }}" class="btn btn-outline-secondary">Export Excel</a>
        </div>
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
                    <div class="text-muted small">Total Credited</div>
                    <div class="fs-4 fw-bold text-success">₨{{ number_format($stats['total_credited'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pending</div>
                    <div class="fs-4 fw-bold text-warning">₨{{ number_format($stats['pending'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Failed</div>
                    <div class="fs-4 fw-bold text-danger">₨{{ number_format($stats['failed'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Commission</div>
                    <div class="fs-4 fw-bold text-primary">₨{{ number_format($stats['total_commission'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payout Transactions</h5>
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['completed','pending','processing','failed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select name="vendor_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Vendors</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->store_name }}</option>
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
                            <th>Order</th>
                            <th>Gross</th>
                            <th>Commission</th>
                            <th>Credited</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Processed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $payout)
                        <tr>
                            <td>{{ $payout->id }}</td>
                            <td>{{ $payout->vendor?->store_name ?? 'N/A' }}</td>
                            <td>{{ $payout->vendorOrder?->order?->order_number ?? '-' }}</td>
                            <td>₨{{ number_format((float)$payout->gross_amount, 2) }}</td>
                            <td>₨{{ number_format((float)$payout->commission_amount, 2) }}</td>
                            <td class="fw-bold">₨{{ number_format((float)$payout->amount, 2) }}</td>
                            <td>
                                @switch($payout->status)
                                    @case('completed') <span class="badge bg-success">Completed</span> @break
                                    @case('pending') <span class="badge bg-warning text-dark">Pending</span> @break
                                    @case('processing') <span class="badge bg-info">Processing</span> @break
                                    @case('failed') <span class="badge bg-danger">Failed</span> @break
                                    @default <span class="badge bg-secondary">{{ ucfirst($payout->status) }}</span>
                                @endswitch
                            </td>
                            <td><code class="small">{{ $payout->transaction_id }}</code></td>
                            <td>{{ $payout->processed_at?->format('M d, Y H:i') ?? '-' }}</td>
                            <td>
                                @if($payout->status === 'failed')
                                    <form method="POST" action="{{ route('admin.payouts.retry', $payout) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning">Retry</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No payouts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $payouts->links() }}
        </div>
    </div>
</div>
@endsection