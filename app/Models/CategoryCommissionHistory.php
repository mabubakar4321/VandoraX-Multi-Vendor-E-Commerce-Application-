<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryCommissionHistory extends Model
{
    public $timestamps = true;

    protected $table = 'category_commission_history';

    protected $fillable = [
        'category_id',
        'old_commission',
        'new_commission',
        'changed_by',
        'reason',
    ];

    protected $casts = [
        'old_commission' => 'decimal:2',
        'new_commission' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}