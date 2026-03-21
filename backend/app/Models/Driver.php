<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function trafficInfractions()
    {
        return $this->hasMany(TrafficInfraction::class);
    }
}
