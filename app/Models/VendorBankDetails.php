<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankDetails extends Model
{
     use HasFactory;

    protected $fillable = [
        'vendor_id',
        'account_holder_name',
        'account_number',
        'iban',
        'bank_name',
        'account_type',
        'is_primary',
        'is_verified',
        'verification_date',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verification_date' => 'datetime',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Methods
    public function verify()
    {
        $this->update([
            'is_verified' => true,
            'verification_date' => now(),
        ]);
    }

    public function makePrimary()
    {
        VendorBankDetails::where('vendor_id', $this->vendor_id)
            ->update(['is_primary' => false]);
        
        $this->update(['is_primary' => true]);
    }
}
