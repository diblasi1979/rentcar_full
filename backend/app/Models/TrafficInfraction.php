<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficInfraction extends Model
{
    use HasFactory;

    protected $appends = [
        'attachment_url',
    ];

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'infraction_date',
        'report_number',
        'type',
        'description',
        'location',
        'amount',
        'status',
        'payment_date',
        'attachment',
    ];

    protected $casts = [
        'infraction_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment) {
            return null;
        }

        return asset('storage/' . $this->attachment);
    }
}
