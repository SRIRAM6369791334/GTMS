<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'branch_name',
        'ifsc_code',
        'is_default',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    /**
     * Quotations that remit into this bank account.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'bank_account_id');
    }

    /**
     * Creator of this bank profile.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Formatted display label.
     */
    public function getDisplayNameAttribute(): string
    {
        $branch = $this->branch_name ? " ({$this->branch_name})" : '';
        return "{$this->bank_name}{$branch} - A/C: {$this->account_number}";
    }
}
