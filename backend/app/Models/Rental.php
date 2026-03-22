<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    use HasFactory;

    protected $appends = [
        'contract_pdf_url',
    ];

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'type',
        'amount',
        'start_date',
        'contract_from',
        'contract_to',
        'contract_pdf',
        'active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'contract_from' => 'date',
        'contract_to' => 'date',
        'active' => 'boolean',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function getContractPdfUrlAttribute(): ?string
    {
        if (!$this->contract_pdf) {
            return null;
        }

        return asset('storage/' . $this->contract_pdf);
    }
}
