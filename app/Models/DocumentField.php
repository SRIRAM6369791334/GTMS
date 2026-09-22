<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentField extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id',
        'nature_of_work_id',
        'name',
        'required',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'required' => 'boolean',
        'status' => 'integer',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
