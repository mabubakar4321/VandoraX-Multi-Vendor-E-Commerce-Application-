<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Sensitive banking information is hidden
     * from array and JSON output.
     */
    protected $hidden = [
        'account_number',
        'iban',
    ];

    /**
     * Attribute casting.
     *
     * account_number and iban are encrypted automatically
     * when saved and decrypted automatically when accessed.
     */
    protected function casts(): array
    {
        return [
            'account_number'    => 'encrypted',
            'iban'              => 'encrypted',
            'is_primary'        => 'boolean',
            'is_verified'       => 'boolean',
            'verification_date' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Verification
    |--------------------------------------------------------------------------
    */

    public function verify(): void
    {
        $this->update([
            'is_verified'       => true,
            'verification_date' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Primary Bank Account
    |--------------------------------------------------------------------------
    */

    public function makePrimary(): void
    {
        static::where('vendor_id', $this->vendor_id)
            ->where('id', '!=', $this->id)
            ->update([
                'is_primary' => false,
            ]);

        $this->update([
            'is_primary' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Account Number Masking
    |--------------------------------------------------------------------------
    */

    public function maskedAccountNumber(): string
    {
        $accountNumber = $this->account_number ?? '';

        return str_repeat(
            '*',
            max(0, strlen($accountNumber) - 4)
        ) . substr($accountNumber, -4);
    }
}