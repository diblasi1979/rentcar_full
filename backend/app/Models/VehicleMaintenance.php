<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VehicleMaintenance extends Model
{
    use HasFactory;

    protected $appends = [
        'receipt_url',
    ];

    protected $fillable = [
        'vehicle_id',
        'maintenance_date',
        'type',
        'description',
        'mileage',
        'next_service_mileage',
        'cost',
        'status',
        'receipt',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'mileage' => 'integer',
        'next_service_mileage' => 'integer',
        'cost' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function serviceRequest(): HasOne
    {
        return $this->hasOne(ServiceRequest::class);
    }

    public function getReceiptUrlAttribute(): ?string
    {
        if (!$this->receipt) {
            return null;
        }

        return asset('storage/' . $this->receipt);
    }
}