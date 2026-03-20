<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'type',
        'amount',
        'start_date',
        'contract_from',
        'contract_to',
        'active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'contract_from' => 'date',
        'contract_to' => 'date',
        'active' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
