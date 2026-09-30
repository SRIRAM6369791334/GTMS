<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'quotations';

    protected $fillable = [
        'quotation_number',
        'customer_id',
        'lease_application_id',
        'customer_name',
        'company_name',
        'phone',
        'email',
        'gst_number',
        'address',
        'quarry_name',
        'district_id',
        'taluk',
        'village',
        'survey_numbers',
        'area_extent_ha',
        'mineral_name',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'validity_days',
        'payment_terms',
        'exclusions',
        'notes',
        'status',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'tax_rate'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'area_extent_ha' => 'decimal:4',
        'validity_days'  => 'integer',
    ];

    /**
     * Relationship: Owning Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Relationship: Precursor or Associated Lease Application
     */
    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    /**
     * Relationship: Revenue District
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Relationship: Multi-service Line Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * Relationship: Creator User
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
     * Relationship: Payment Receipts linked to this Quotation
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    /**
     * Calculate and sync subtotal, tax amount, and total amount from items.
     */
    public function calculateTotals(): self
    {
        $subtotal = $this->items->sum('subtotal');
        $taxRate = $this->tax_rate ?? 18.00;
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $totalAmount = $subtotal + $taxAmount;

        $this->subtotal = $subtotal;
        $this->tax_rate = $taxRate;
        $this->tax_amount = $taxAmount;
        $this->total_amount = $totalAmount;

        return $this;
    }

    /**
     * Convert Total Amount to Indian Currency in Words (Lakhs, Crores, Rupees).
     */
    public function getAmountInWordsAttribute(): string
    {
        return self::convertToIndianCurrencyWords((float) $this->total_amount);
    }

    /**
     * Indian numbering system amount to words converter.
     */
    public static function convertToIndianCurrencyWords(float $number): string
    {
        $number = round($number, 2);
        $no = floor($number);
        $decimal = round(($number - $no) * 100);

        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
            50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty',
            90 => 'Ninety'
        ];

        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        $divider = [10000000, 100000, 1000, 100, 1];
        $res = [];

        foreach ($divider as $i => $d) {
            if ($no >= $d) {
                $val = (int) ($no / $d);
                $no %= $d;
                if ($val > 0) {
                    if ($val < 20) {
                        $chunk = $words[$val];
                    } elseif ($val < 100) {
                        $chunk = $words[((int)($val / 10)) * 10] . (($val % 10) ? ' ' . $words[$val % 10] : '');
                    } else {
                        $chunk = $words[(int)($val / 100)] . ' Hundred' . (($val % 100) ? ' and ' . self::convertUnderHundred($val % 100, $words) : '');
                    }
                    $res[] = $chunk . ($digits[4 - $i] ? ' ' . $digits[4 - $i] : '');
                }
            }
        }

        $str = implode(' ', $res) ?: 'Zero';
        $paise = ($decimal > 0) ? ' and ' . self::convertUnderHundred((int) $decimal, $words) . ' Paise' : '';

        return 'Rupees ' . trim($str) . $paise . ' Only';
    }

    private static function convertUnderHundred(int $val, array $words): string
    {
        if ($val < 20) {
            return $words[$val];
        }
        return $words[((int)($val / 10)) * 10] . (($val % 10) ? ' ' . $words[$val % 10] : '');
    }
}
