<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceCoverage extends Model
{
    use HasFactory;

    protected $appends = [
        'policy_pdf_url',
        'credential_image_url',
    ];

    protected $fillable = [
        'vehicle_id',
        'policy_number',
        'endorsement_number',
        'insured_last_name',
        'insured_first_name',
        'insured_document_type',
        'insured_document_number',
        'insurance_company',
        'contact_phone',
        'electronic_payment_code',
        'valid_from',
        'valid_to',
        'policy_pdf',
        'credential_image',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getPolicyPdfUrlAttribute(): ?string
    {
        if (!$this->policy_pdf) {
            return null;
        }

        return asset('storage/' . $this->policy_pdf);
    }

    public function getCredentialImageUrlAttribute(): ?string
    {
        if (!$this->credential_image) {
            return null;
        }

        return asset('storage/' . $this->credential_image);
    }
}
