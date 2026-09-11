<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DroneSurvey extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'drone_surveys';

    protected $fillable = [
        'survey_no',
        'customer_id',
        'lease_application_id',
        'mining_application_id',
        'lease_area',
        'location',
        'flight_date',
        'drone_pilot_name',
        'pilot_rpc_no',
        'drone_uin_no',
        'drone_model',
        'altitude_meters',
        'gsd_cm_px',
        'extracted_volume_cbm',
        'survey_status',
        'deliverable_files_path',
        'gtms_report_file',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'flight_date' => 'date',
        'lease_area' => 'decimal:2',
        'altitude_meters' => 'decimal:2',
        'gsd_cm_px' => 'decimal:2',
        'extracted_volume_cbm' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    public function miningApplication(): BelongsTo
    {
        return $this->belongsTo(MiningApplication::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DroneDocument::class);
    }
}
