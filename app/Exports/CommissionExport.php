<?php

namespace App\Exports;

use App\Models\Commission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommissionExport implements FromCollection, WithHeadings
{
    protected ?array $filters;

    public function __construct(?array $filters = null)
    {
        $this->filters = $filters;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $query = Commission::with(['vendorOrder.vendor', 'vendorOrder.order']);

        if (!empty($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->where('created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }
        if (!empty($this->filters['vendor_id'])) {
            $query->whereHas('vendorOrder', fn ($q) => $q->where('vendor_id', $this->filters['vendor_id']));
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->get()
            ->map(fn ($c) => [
                'Commission ID' => $c->id,
                'Order Number' => $c->vendorOrder?->order?->order_number,
                'Vendor Order' => $c->vendorOrder?->vendor_order_number,
                'Vendor' => $c->vendorOrder?->vendor?->store_name ?? 'Vendor #' . $c->vendorOrder?->vendor_id,
                'Gross Amount' => number_format((float) $c->order_amount, 2),
                'Rate (%)' => number_format((float) $c->commission_rate, 2),
                'Commission' => number_format((float) $c->commission_amount, 2),
                'Vendor Payout' => number_format((float) $c->vendor_payout, 2),
                'Status' => $c->status,
                'Date' => $c->created_at?->format('Y-m-d H:i'),
            ]);
    }

    public function headings(): array
    {
        return [
            'Commission ID', 'Order Number', 'Vendor Order', 'Vendor',
            'Gross Amount (PKR)', 'Rate (%)', 'Commission (PKR)', 'Vendor Payout (PKR)',
            'Status', 'Date',
        ];
    }
}
