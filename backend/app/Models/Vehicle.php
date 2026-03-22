<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'plate',
        'year',
        'has_gnc',
    ];

    protected $casts = [
        'has_gnc' => 'boolean',
        'year' => 'integer',
    ];

    public function insuranceCoverages(): HasMany
    {
        return $this->hasMany(InsuranceCoverage::class);
    }

    public function trafficInfractions(): HasMany
    {
        return $this->hasMany(TrafficInfraction::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function assignedDrivers(): HasMany
    {
        return $this->hasMany(Driver::class, 'assigned_vehicle_id');
    }
}
