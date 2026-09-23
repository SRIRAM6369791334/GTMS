<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApplicationHandler extends Model
{
    use HasFactory;

    protected $table = 'application_handlers';

    protected $fillable = [
        'application_type',
        'application_id',
        'handlerable_type',
        'handlerable_id',
        'name',
        'role',
        'notes',
        'sort_order',
    ];

    /**
     * Polymorphic parent relation
     */
    public function handlerable(): MorphTo
    {
        return $this->morphTo();
    }
}
