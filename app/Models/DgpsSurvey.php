<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DgpsSurvey extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'dgps_surveys';

    protected $fillable = [
        'survey_no',
        'field_book_no',
        'customer_id',
        'lease_application_id',
        'mining_application_id',
        'lease_area_ha',
        'surveyed_area_ha',
        'area_discrepancy_ha',
        'location',
        'survey_date',
        'surveyor_user_id',
        'survey_team_notes',
        'instrument_model',
        'instrument_serial_no',
        'survey_status',
        'report_status',
        'gtm_report_file',
        'autocad_dwg_file',
        'product_value',
        'paid_amount',
        'pending_amount',
        'payment_status',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'survey_date'         => 'date',
        'lease_area_ha'       => 'decimal:2',
        'surveyed_area_ha'    => 'decimal:2',
        'area_discrepancy_ha' => 'decimal:2',
        'product_value'       => 'decimal:2',
        'paid_amount'         => 'decimal:2',
        'pending_amount'      => 'decimal:2',
    ];

    public function handlers(): HasMany
    {
        return $this->hasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'dgps')->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'dgps');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    public function miningApplication(): BelongsTo
    {
        return $this->belongsTo(MiningApplication::class);
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function points(): HasMany
    {
        return $this->hasMany(DgpsPoint::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DgpsDocument::class);
    }
}
