<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'full_name',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city',
        'province',
        'postal_code',
        'country',
        'delivery_instructions',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Methods
    public function getFullAddress()
    {
        $address = $this->address_line_1;
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        $address .= ', ' . $this->city . ', ' . $this->province . ' ' . $this->postal_code;
        return $address;
    }

    public function getFormatted()
    {
        return [
            'name' => $this->full_name,
            'phone' => $this->phone_number,
            'address' => $this->getFullAddress(),
            'country' => $this->country,
        ];
    }
}
