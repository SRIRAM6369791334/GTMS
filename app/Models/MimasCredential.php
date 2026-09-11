<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MimasCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_application_id',
        'user_id',
        'password',
        'email',
        'contact_number',
        'mimas_ack_no',
        'ack_date',
        'portal_status',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'ack_date' => 'date',
    ];

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }
}
