<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $appends = [
        'payment_receipt_url',
    ];

    protected $fillable = [
        'rental_id',
        'amount',
        'payment_date',
        'km_reported',
        'payment_receipt',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function getPaymentReceiptUrlAttribute(): ?string
    {
        if (!$this->payment_receipt) {
            return null;
        }

        return asset('storage/' . $this->payment_receipt);
    }
}
