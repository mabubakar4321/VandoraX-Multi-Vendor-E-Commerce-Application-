<?php

namespace App\Exports;

use App\Models\VendorPayout;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayoutExport implements FromCollection, WithHeadings
{
    protected ?int $vendorId;

    public function __construct(?int $vendorId = null)
    {
        $this->vendorId = $vendorId;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $query = VendorPayout::with(['vendor', 'vendorOrder.order']);
        if ($this->vendorId) {
            $query->where('vendor_id', $this->vendorId);
        }

        return $query->latest()->get()
            ->map(fn ($p) => [
                'Payout ID' => $p->id,
                'Vendor' => $p->vendor?->store_name ?? 'Vendor #' . $p->vendor_id,
                'Order' => $p->vendorOrder?->order?->order_number,
                'Gross (PKR)' => number_format((float) $p->gross_amount, 2),
                'Commission (PKR)' => number_format((float) $p->commission_amount, 2),
                'Credited (PKR)' => number_format((float) $p->amount, 2),
                'Method' => $p->payout_method,
                'Status' => $p->status,
                'Reference' => $p->transaction_id,
                'Processed' => $p->processed_at?->format('Y-m-d H:i'),
            ]);
    }

    public function headings(): array
    {
        return [
            'Payout ID', 'Vendor', 'Order', 'Gross (PKR)', 'Commission (PKR)',
            'Credited (PKR)', 'Method', 'Status', 'Reference', 'Processed',
        ];
    }
}
