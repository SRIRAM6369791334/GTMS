<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApplicationPayment extends Model
{
    use HasFactory;

    protected $table = 'application_payments';

    protected $fillable = [
        'application_type',
        'application_id',
        'payable_type',
        'payable_id',
        'product_value',
        'paid_amount',
        'pending_amount',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'product_value'  => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'pending_amount' => 'decimal:2',
    ];

    /**
     * Polymorphic relation to any application model (LeaseApplication, MiningApplication, etc.)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
