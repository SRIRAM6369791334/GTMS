<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PptApplication extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'ppt_applications';

    protected $fillable = [
        'application_no',
        'customer_id',
        'environment_project_id',
        'presentation_stage',
        'project_name',
        'district_id',
        'taluk_village',
        'mineral_id',
        'status',
        'product_value',
        'paid_amount',
        'pending_amount',
        'payment_status',
        'rqp_attending',
        'company_rep_attending',
        'rep_mobile',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'product_value'  => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'pending_amount' => 'decimal:2',
    ];

    public function handlers(): HasMany
    {
        return $this->hasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'ppt')->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'ppt');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function environmentProject(): BelongsTo
    {
        return $this->belongsTo(EnvironmentProject::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function mineral(): BelongsTo
    {
        return $this->belongsTo(Mineral::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(PptAgenda::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PptDocument::class);
    }
}
