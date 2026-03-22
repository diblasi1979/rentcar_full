<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dni',
        'license_number',
        'license_expiration',
        'phone',
        'email',
        'assigned_vehicle_id',
        'documents',
        'enabled',
    ];

    protected $casts = [
        'documents' => 'array',
        'license_expiration' => 'date',
        'documents' => 'array',
        'license_expiration' => 'date',
        'enabled' => 'boolean',
    ];

    public function trafficInfractions(): HasMany
    {
        return $this->hasMany(TrafficInfraction::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function assignedVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
