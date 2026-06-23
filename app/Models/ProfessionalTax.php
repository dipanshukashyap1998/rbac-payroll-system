<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalTax extends Model
{
    protected $table = 'professional_taxes';

    protected $fillable = [
        'state',
        'income_from',
        'income_to',
        'tax_amount',
        'description',
        'is_active',
    ];

    protected $casts = [
        'income_from' => 'decimal:2',
        'income_to' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
