<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnStatusHistory extends Model
{
    public $timestamps = false;

    protected $table = 'return_status_history';

    protected $fillable = ['return_id', 'old_status', 'new_status', 'notes', 'changed_by', 'changed_at'];

    protected $casts = ['changed_at' => 'datetime'];

    public function returnRequest()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
