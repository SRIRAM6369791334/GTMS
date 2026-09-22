<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseApplication extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $fillable = [
        'common_id',
        'application_no',
        'customer_id',
        'district_id',
        'category_id',
        'mineral_id',
        'other_mineral_name',
        'taluk',
        'village',
        'area_extent_ha',
        'area_extent_acres',
        'start_date',
        'end_date',
        'lease_period_years',
        'contact_person',
        'secondary_contact_person',
        'contact_mobile',
        'secondary_contact_mobile',
        'current_step',
        'status',
        'go_number',
        'go_date',
        'go_file',
        'rejection_note',
        'assigned_inspector_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'area_extent_ha' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'go_date' => 'date',
        'current_step' => 'integer',
        'lease_period_years' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LeaseCategory::class, 'category_id');
    }

    public function mineral(): BelongsTo
    {
        return $this->belongsTo(Mineral::class);
    }

    public function minerals(): BelongsToMany
    {
        return $this->belongsToMany(Mineral::class, 'lease_application_minerals');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_inspector_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function surveyNumbers(): HasMany
    {
        return $this->hasMany(LeaseSurveyNumber::class);
    }

    public function mimasCredentials(): HasMany
    {
        return $this->hasMany(MimasCredential::class);
    }

    public function mimasCredential(): HasOne
    {
        return $this->hasOne(MimasCredential::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeaseDocument::class);
    }

    public function miningApplications(): HasMany
    {
        return $this->hasMany(MiningApplication::class);
    }

    public function environmentProjects(): HasMany
    {
        return $this->hasMany(EnvironmentProject::class);
    }

    public function dgpsSurveys(): HasMany
    {
        return $this->hasMany(DgpsSurvey::class);
    }

    public function droneSurveys(): HasMany
    {
        return $this->hasMany(DroneSurvey::class);
    }

    public function stockpile(): HasOne
    {
        return $this->hasOne(MineralStockpile::class);
    }
}
