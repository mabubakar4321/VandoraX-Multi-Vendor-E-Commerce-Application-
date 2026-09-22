<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBulkImport extends Model
{
    protected $fillable = [
        'vendor_id', 'file_name', 'file_path',
        'status', 'total_rows', 'successful_rows',
        'failed_rows', 'error_report',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'successful_rows' => 'integer',
        'failed_rows' => 'integer',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getSuccessRate()
    {
        if ($this->total_rows == 0) return 0;
        return round(($this->successful_rows / $this->total_rows) * 100, 2);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }
}