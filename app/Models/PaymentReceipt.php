<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentReceipt extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'payment_receipts';

    protected $fillable = [
        'receipt_number',
        'customer_id',
        'quotation_id',
        'application_type',
        'application_id',
        'amount_paid',
        'balance_due',
        'previous_paid',
        'payment_mode',
        'bank_name',
        'reference_number',
        'transaction_date',
        'notes',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'amount_paid'      => 'decimal:2',
        'balance_due'      => 'decimal:2',
        'previous_paid'    => 'decimal:2',
        'transaction_date' => 'date',
    ];

    /**
     * Relationship: Owning Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Relationship: Optional Linked Quotation
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Relationship: Officer / User who generated or recorded the receipt
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Dynamic Accessor: Resolves the underlying statutory application model instance.
     */
    public function getApplicationAttribute()
    {
        if (!$this->application_type || !$this->application_id) {
            return null;
        }

        $type = strtolower(trim($this->application_type));
        $classMap = [
            'lease'          => LeaseApplication::class,
            'mining'         => MiningApplication::class,
            'environment'    => EnvironmentProject::class,
            'ppt'            => PptApplication::class,
            'dgps'           => DgpsSurvey::class,
            'drone'          => DroneSurvey::class,
            'ec'             => EcCertificate::class,
            'ec_certificate' => EcCertificate::class,
            'ec_compliance'  => EcCompliance::class,
        ];

        if (isset($classMap[$type])) {
            return $classMap[$type]::find($this->application_id);
        }

        return null;
    }

    /**
     * Dynamic Accessor: Human-readable application reference identifier.
     */
    public function getApplicationReferenceAttribute(): string
    {
        $app = $this->application;
        if (!$app) {
            return $this->application_type ? ucfirst($this->application_type) . ' #' . $this->application_id : 'General / Direct Receipt';
        }

        return $app->application_no
            ?? $app->project_code
            ?? $app->survey_no
            ?? $app->ec_ref_no
            ?? $app->compliance_no
            ?? ('#' . $app->id);
    }

    /**
     * Dynamic Accessor: Computes the 1-based sequential installment number for this application.
     */
    public function getInstallmentNumberAttribute(): int
    {
        if (!$this->application_type || !$this->application_id) {
            return 1;
        }

        $type = strtolower(trim($this->application_type));
        return static::where('customer_id', $this->customer_id)
            ->where(function ($q) use ($type) {
                $q->where('application_type', $type);
                if ($type === 'ec' || $type === 'ec_certificate') {
                    $q->orWhere('application_type', 'ec')->orWhere('application_type', 'ec_certificate');
                }
            })
            ->where('application_id', $this->application_id)
            ->where('id', '<=', $this->id)
            ->count() ?: 1;
    }

    /**
     * Dynamic Accessor: Total number of receipts recorded for this application.
     */
    public function getTotalInstallmentsAttribute(): int
    {
        if (!$this->application_type || !$this->application_id) {
            return 1;
        }

        $type = strtolower(trim($this->application_type));
        return static::where('customer_id', $this->customer_id)
            ->where(function ($q) use ($type) {
                $q->where('application_type', $type);
                if ($type === 'ec' || $type === 'ec_certificate') {
                    $q->orWhere('application_type', 'ec')->orWhere('application_type', 'ec_certificate');
                }
            })
            ->where('application_id', $this->application_id)
            ->count() ?: 1;
    }

    /**
     * Dynamic Accessor: Amount paid in Indian Currency Words.
     */
    public function getAmountInWordsAttribute(): string
    {
        return Quotation::convertToIndianCurrencyWords((float) $this->amount_paid);
    }

    /**
     * Collision-resistant sequential receipt number generator.
     * Format: GTMS/REC/{YYYY}/{0001}
     */
    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $prefix = "GTMS/REC/{$year}/";

        $lastReceipt = static::withoutGlobalScopes()
            ->withTrashed()
            ->where('receipt_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($lastReceipt && ($pos = strrpos($lastReceipt->receipt_number, '/')) !== false) {
            $lastSeq = (int) substr($lastReceipt->receipt_number, $pos + 1);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        $number = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScopes()->withTrashed()->where('receipt_number', $number)->exists()) {
            $nextSeq++;
            $number = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
        }

        return $number;
    }
}
