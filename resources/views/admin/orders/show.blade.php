@extends('layouts.admin')
@section('title', 'Order Details — Admin')

@section('content')
@php
    $payment = $order->payment;
    $paymentMethod = $payment?->payment_method;
    $paymentLabel = match ($paymentMethod) {
        'bank_transfer' => 'Bank Transfer',
        'wallet', 'jazzcash' => 'JazzCash',
        default => $payment ? ucfirst(str_replace('_', ' ', $paymentMethod ?? 'Unknown')) : 'Cash on Delivery',
    };
@endphp

<div class="min-w-0 space-y-8">
    @if(session('success'))
        <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" style="border-radius: 2px;">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-800" style="border-radius: 2px;">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" style="border-radius: 2px;">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">← Back to all orders</a>
            <p class="mt-5 text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Admin order monitor</p>
            <h1 class="mt-2 break-all text-3xl leading-tight">{{ $order->order_number }}</h1>
            <p class="mt-2 text-sm text-graphite/70">Placed {{ $order->created_at->format('F d, Y \\a\\t g:i A') }}</p>
        </div>
        <span class="inline-flex w-fit rounded-full bg-mist px-3 py-1.5 text-sm font-semibold capitalize text-forest">{{ $order->effective_status }}</span>
    </div>

    <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl">Update order status</h2>
                <p class="mt-1 text-sm text-graphite/70">This synchronizes the customer order and every vendor sub-order.</p>
            </div>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex flex-col gap-2 sm:flex-row">
                @csrf
                @method('PATCH')
                <select name="status" class="border border-mist px-4 py-2.5 text-sm capitalize" style="border-radius: 2px;">
                    @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary px-5 py-2.5 text-sm">Save status</button>
            </form>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Customer details</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Name</p><p class="mt-2 font-semibold text-forest">{{ $order->customer?->name ?? 'Customer' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Email</p><p class="mt-2 break-all text-forest">{{ $order->customer?->email ?? 'Not provided' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Customer ID</p><p class="mt-2 text-forest">{{ $order->customer_id }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Account phone</p><p class="mt-2 text-forest">{{ $order->customer?->phone_number ?? 'Not provided' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Account status</p><p class="mt-2 capitalize text-forest">{{ $order->customer?->status ?? 'Unknown' }}{{ $order->customer && !$order->customer->is_active ? ' (disabled)' : '' }}</p></div>
                </div>
            </section>

            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Delivery address</h2>
                <div class="mt-5 break-words text-sm leading-6 text-graphite">
                    <p class="font-semibold text-forest">{{ $order->address?->full_name ?? '' }}</p>
                    <p>{{ $order->address?->address_line_1 ?? '' }}@if($order->address?->address_line_2), {{ $order->address->address_line_2 }}@endif</p>
                    <p>{{ $order->address?->city ?? '' }}, {{ $order->address?->province ?? '' }} {{ $order->address?->postal_code ?? '' }}</p>
                    <p>{{ $order->address?->country ?? '' }}</p>
                    <p class="mt-2 font-semibold text-forest">{{ $order->address?->phone_number ?? '' }}</p>
                    @if($order->address?->delivery_instructions)
                        <div class="mt-4 border-t border-mist pt-4">
                            <p class="font-semibold text-forest">Delivery instructions</p>
                            <p>{{ $order->address->delivery_instructions }}</p>
                        </div>
                    @endif
                </div>
            </section>

            <section class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
                <div class="border-b border-mist px-5 py-4 sm:px-6">
                    <h2 class="text-xl">Products</h2>
                    <p class="mt-1 text-sm text-graphite/65">Item, seller, pricing, and fulfillment information for this order.</p>
                </div>
                <div class="divide-y divide-mist">
                    @forelse($order->items as $item)
                        @php
                            $product = $item->product;
                            $mainImage = $product?->images->firstWhere('is_main', true) ?? $product?->images->first();
                        @endphp
                        <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:px-6">
                            <div class="h-20 w-20 shrink-0 overflow-hidden bg-paper" style="border-radius: 2px;">
                                @if($mainImage)
                                    <img src="{{ asset('storage/' . $mainImage->image_path) }}" alt="{{ $product?->name ?? 'Product' }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-xs text-graphite/50">No image</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="break-words font-semibold text-forest">{{ $product?->name ?? 'Product no longer available' }}</h3>
                                <div class="mt-2 grid gap-x-5 gap-y-1 text-sm text-graphite sm:grid-cols-2">
                                    <p>SKU: <span class="font-medium text-forest">{{ $product?->sku ?? 'Not available' }}</span></p>
                                    <p>Category: <span class="font-medium text-forest">{{ $product?->category?->name ?? 'Uncategorized' }}</span></p>
                                    <p>Product status: <span class="capitalize font-medium text-forest">{{ $product?->status ?? 'Unavailable' }}</span></p>
                                    <p>Item status: <span class="capitalize font-medium text-forest">{{ $item->status }}</span></p>
                                </div>
                                @if($product?->description)
                                    <p class="mt-2 text-sm leading-5 text-graphite">{{ $product->description }}</p>
                                @endif
                                @if($item->variant)
                                    <p class="mt-2 text-sm text-graphite">Variant: <span class="font-medium text-forest">{{ $item->variant->variant_value }}</span></p>
                                @endif
                                <div class="mt-3 flex flex-wrap gap-5 text-sm text-graphite">
                                    <span>Qty: <strong class="text-forest">{{ $item->quantity }}</strong></span>
                                    <span>Unit: <strong class="text-forest">Rs {{ number_format($item->price_at_purchase) }}</strong></span>
                                    @if($item->return_requested)
                                        <span class="font-semibold text-vermilion">Return requested</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-graphite">Vendor: <span class="font-semibold text-forest">{{ $product?->vendor?->store_name ?? $product?->vendor?->user?->name ?? 'Vendor' }}</span></p>
                            </div>
                            <div class="shrink-0 text-left sm:text-right">
                                <p class="font-display text-xl text-forest">Rs {{ number_format($item->total_amount) }}</p>
                                <p class="mt-1 text-xs text-graphite/60">Line total</p>
                            </div>
                        </div>
                    @empty
                        <p class="px-6 py-8 text-sm text-graphite/65">No products found.</p>
                    @endforelse
                    <div class="flex flex-wrap justify-between gap-3 border-t border-mist bg-paper px-5 py-4 font-semibold text-forest sm:px-6">
                        <span>Order total</span><span>Rs {{ number_format($order->total_amount) }}</span>
                    </div>
                </div>
            </section>

            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Vendors on this order</h2>
                <div class="mt-5 space-y-4">
                    @forelse($order->vendorOrders as $vendorOrder)
                        @php
                            $vendor = $vendorOrder->vendor;
                            $vendorItems = $order->items->filter(fn ($item) => $item->product?->vendor_id === $vendorOrder->vendor_id);
                        @endphp
                        <div class="border border-mist p-4" style="border-radius: 2px;">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-forest">{{ $vendor?->store_name ?? $vendor?->user?->name ?? 'Vendor' }}</p>
                                    <p class="mt-1 break-all text-xs text-graphite/60">Vendor order: {{ $vendorOrder->vendor_order_number }}</p>
                                    <p class="mt-1 text-xs text-graphite/60">Vendor ID: {{ $vendorOrder->vendor_id }}</p>
                                </div>
                                <span class="rounded-full bg-mist px-2.5 py-1 text-xs font-semibold capitalize text-forest">{{ $vendorOrder->status }}</span>
                            </div>
                            <div class="mt-4 border-t border-mist pt-4">
                                <p class="text-sm font-semibold text-forest">Products in this vendor order</p>
                                <ul class="mt-2 space-y-1 text-sm text-graphite">
                                    @forelse($vendorItems as $vendorItem)
                                        <li class="break-words">{{ $vendorItem->product?->name ?? 'Product no longer available' }} × {{ $vendorItem->quantity }}</li>
                                    @empty
                                        <li>No matching product items found.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                                <div><p class="text-graphite/60">Store owner</p><p class="mt-1 font-medium text-forest">{{ $vendor?->user?->name ?? 'Not available' }}</p></div>
                                <div><p class="text-graphite/60">Owner email</p><p class="mt-1 break-all font-medium text-forest">{{ $vendor?->user?->email ?? 'Not available' }}</p></div>
                                <div><p class="text-graphite/60">Owner phone</p><p class="mt-1 font-medium text-forest">{{ $vendor?->user?->phone_number ?? 'Not provided' }}</p></div>
                                <div><p class="text-graphite/60">Store URL</p><p class="mt-1 break-all font-medium text-forest">{{ $vendor?->store_slug ? url('/store/' . $vendor->store_slug) : 'Not available' }}</p></div>
                                <div><p class="text-graphite/60">Category</p><p class="mt-1 font-medium text-forest">{{ $vendor?->category?->name ?? 'Unassigned' }}</p></div>
                                <div><p class="text-graphite/60">Store status / commission rate</p><p class="mt-1 capitalize font-medium text-forest">{{ $vendor?->status ?? 'Unavailable' }} / {{ $vendor?->commission_rate ?? '0.00' }}%</p></div>
                                    <div class="sm:col-span-2"><p class="text-graphite/60">Store description</p><p class="mt-1 break-words whitespace-pre-line text-forest">{{ $vendor?->store_description ?: 'No description provided' }}</p></div>
                                <div><p class="text-graphite/60">Confirmed</p><p class="mt-1 text-forest">{{ $vendorOrder->confirmed_at?->format('M d, Y H:i') ?? 'Not confirmed' }}</p></div>
                                <div><p class="text-graphite/60">Shipped / delivered</p><p class="mt-1 text-forest">{{ $vendorOrder->shipped_at?->format('M d, Y H:i') ?? 'Not shipped' }} / {{ $vendorOrder->delivered_at?->format('M d, Y H:i') ?? 'Not delivered' }}</p></div>
                                <div><p class="text-graphite/60">Tracking number</p><p class="mt-1 break-all text-forest">{{ $vendorOrder->tracking_number ?? 'Not provided' }}</p></div>
                                <div>
                                    <p class="text-graphite/60">Tracking URL</p>
                                    @if($vendorOrder->tracking_url)
                                        <a href="{{ $vendorOrder->tracking_url }}" target="_blank" rel="noopener noreferrer" class="mt-1 break-all text-indigo-600 hover:underline">{{ $vendorOrder->tracking_url }}</a>
                                    @else
                                        <p class="mt-1 text-forest">Not provided</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 grid gap-3 border-t border-mist pt-4 text-sm sm:grid-cols-3">
                                <div><p class="text-graphite/60">Subtotal</p><p class="mt-1 font-semibold text-forest">Rs {{ number_format($vendorOrder->subtotal) }}</p></div>
                                <div><p class="text-graphite/60">Commission</p><p class="mt-1 font-semibold text-forest">Rs {{ number_format($vendorOrder->commission_amount) }}</p></div>
                                <div><p class="text-graphite/60">Vendor amount</p><p class="mt-1 font-semibold text-forest">Rs {{ number_format($vendorOrder->vendor_amount) }}</p></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-graphite/65">No vendor allocations found.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Payment details</h2>
                <div class="mt-5 space-y-4 text-sm">
                    <div class="flex flex-wrap justify-between gap-2"><span class="text-graphite/70">Method</span><strong class="text-forest">{{ $paymentLabel }}</strong></div>
                    <div class="flex flex-wrap justify-between gap-2"><span class="text-graphite/70">Order payment</span><strong class="capitalize text-forest">{{ $order->payment_status }}</strong></div>
                    <div class="flex flex-wrap justify-between gap-2"><span class="text-graphite/70">Payment record</span><strong class="capitalize text-forest">{{ $payment?->status ?? 'Not required' }}</strong></div>

                    @if($payment && $paymentMethod === 'bank_transfer')
                        @foreach ([['Customer name', $payment->bank_payer_name], ['Payment app/platform', $payment->bank_payment_app], ['Payment source', $payment->bank_payment_source], ['Customer account number', $payment->bank_customer_account_number]] as [$label, $value])
                            <div class="border-t border-mist pt-3"><p class="text-xs text-graphite/60">{{ $label }}</p><p class="mt-1 break-words font-medium text-forest">{{ $value ?: 'Not provided' }}</p></div>
                        @endforeach
                    @endif

                    @if($payment)
                        <div class="border-t border-mist pt-3"><p class="text-xs text-graphite/60">Transaction ID</p><p class="mt-1 break-all font-medium text-forest">{{ $payment->transaction_id ?? 'Not provided' }}</p></div>
                        @if($payment->paid_at)
                            <div class="border-t border-mist pt-3"><p class="text-xs text-graphite/60">Paid at</p><p class="mt-1 font-medium text-forest">{{ $payment->paid_at->format('M d, Y H:i') }}</p></div>
                        @endif
                        <div class="border-t border-mist pt-3"><p class="text-xs text-graphite/60">Payment amount</p><p class="mt-1 font-medium text-forest">{{ $payment->currency }} {{ number_format($payment->amount) }}</p></div>
                    @endif

                    @if($payment && $payment->payment_slip_path)
                        <button type="button" onclick="openAdminPaymentSlip()" class="btn-dark mt-2 w-full px-4 py-2.5">View payment slip</button>
                    @endif
                </div>
            </section>

            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Order information</h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <div><dt class="text-graphite/60">Order status</dt><dd class="mt-1 capitalize text-forest">{{ $order->effective_status }}</dd></div>
                    <div><dt class="text-graphite/60">Payment status</dt><dd class="mt-1 capitalize text-forest">{{ $order->payment_status }}</dd></div>
                    <div><dt class="text-graphite/60">Total items</dt><dd class="mt-1 text-forest">{{ $order->items->sum('quantity') }}</dd></div>
                    <div><dt class="text-graphite/60">Vendor count</dt><dd class="mt-1 text-forest">{{ $order->vendorOrders->count() }}</dd></div>
                    <div><dt class="text-graphite/60">Commission total</dt><dd class="mt-1 text-forest">Rs {{ number_format($order->commission_amount) }}</dd></div>
                    @if($order->notes)
                        <div><dt class="text-graphite/60">Order notes</dt><dd class="mt-1 whitespace-pre-line text-forest">{{ $order->notes }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="border border-red-200 bg-red-50 p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl text-red-900">Danger zone</h2>
                <p class="mt-2 text-sm text-red-800">Delete this order and its related records.</p>
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="mt-5" onsubmit="return confirm('Delete this order permanently?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex w-full items-center justify-center bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700" style="border-radius: 2px;">Delete order</button>
                </form>
            </section>
        </aside>
    </div>
</div>

@if($payment && $payment->payment_slip_path)
    <div id="adminPaymentSlipModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-forest-900/80 p-4" role="dialog" aria-modal="true" aria-label="Payment slip preview" onclick="closeAdminPaymentSlip(event)">
        <div class="relative flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden bg-white p-3 shadow-2xl sm:p-5" style="border-radius: 2px;" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between gap-4 border-b border-mist pb-3">
                <h2 class="text-xl">Payment slip preview</h2>
                <button type="button" onclick="closeAdminPaymentSlip()" class="text-2xl leading-none text-graphite" aria-label="Close payment slip">&times;</button>
            </div>
            <div class="mt-4 min-h-0 overflow-auto bg-paper p-2 text-center sm:p-4">
                <img src="{{ asset('storage/' . $payment->payment_slip_path) }}" alt="Payment slip" class="mx-auto max-h-[72vh] max-w-full object-contain">
            </div>
        </div>
    </div>
    <script>
        function openAdminPaymentSlip() {
            const modal = document.getElementById('adminPaymentSlipModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeAdminPaymentSlip(event) {
            if (event && event.target !== event.currentTarget) return;
            const modal = document.getElementById('adminPaymentSlipModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeAdminPaymentSlip();
        });
    </script>
@endif
@endsection
