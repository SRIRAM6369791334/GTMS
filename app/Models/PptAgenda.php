<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PptAgenda extends Model
{
    use HasFactory;

    protected $table = 'ppt_agendas';

    protected $fillable = [
        'ppt_application_id',
        'committee_type',
        'meeting_no',
        'item_no',
        'meeting_date',
        'agenda_pdf',
        'mom_pdf',
        'outcome',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function pptApplication(): BelongsTo
    {
        return $this->belongsTo(PptApplication::class);
    }
}
