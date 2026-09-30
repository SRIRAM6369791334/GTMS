<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $table = 'quotation_items';

    protected $fillable = [
        'quotation_id',
        'service_name',
        'sac_code',
        'description',
        'quantity',
        'unit',
        'unit_rate',
        'subtotal',
    ];

    protected $casts = [
        'quantity'  => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'subtotal'  => 'decimal:2',
    ];

    /**
     * Relationship: Parent Quotation
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
