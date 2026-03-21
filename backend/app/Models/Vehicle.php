<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function insuranceCoverages()
    {
        return $this->hasMany(InsuranceCoverage::class);
    }

    public function trafficInfractions()
    {
        return $this->hasMany(TrafficInfraction::class);
    }

    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }
}
