<?php

namespace App\Http\Controllers\Concerns;

use App\Support\PaymentConsistency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Shared pieces for the session-wizard modules (PPT, DGPS, Drone, EC Compliance):
 * one validated input set, no fabricated defaults, collision-safe numbers and
 * generic error messages.
 */
trait WizardStoreHelpers
{
    /** Indian mobile number, same rule as Lease / Customers */
    protected string $mobileRule = 'regex:/^[6-9][0-9]{9}$/';

    /**
     * Request values win; anything not sent falls back to the wizard draft.
     */
    protected function wizardInput(Request $request, array $draft, array $keys): array
    {
        $in = [];
        foreach ($keys as $key) {
            $value = $request->input($key);
            $in[$key] = ($value === null || $value === '') ? ($draft[$key] ?? null) : $value;
            if (is_string($in[$key])) {
                $in[$key] = trim($in[$key]);
                if ($in[$key] === '') {
                    $in[$key] = null;
                }
            }
        }
        return $in;
    }

    /**
     * Validate the merged wizard input. On failure send the user to the wizard
     * step that holds the first bad field, with errors for each field.
     *
     * @param array $stepOf field => wizard step number
     */
    protected function validateWizard(array $in, array $rules, array $stepOf, string $stepRoute, array $messages = [])
    {
        $validator = Validator::make($in, $rules, $messages + [
            'primary_phone.regex'   => 'Primary phone must be 10 digits and start with 6, 7, 8 or 9.',
            'secondary_phone.regex' => 'Secondary phone must be 10 digits and start with 6, 7, 8 or 9.',
        ]);
        if ($validator->fails()) {
            $first = array_key_first($validator->errors()->messages());
            $step = $stepOf[$first] ?? 1;
            return redirect()->route($stepRoute, $step)
                ->withErrors($validator)
                ->withInput();
        }
        return null;
    }

    /**
     * Payment values from the wizard: [value, paid, pending, status].
     * Missing amounts are 0 (never a made-up fee). Status must match amounts.
     *
     * @throws ValidationException
     */
    protected function wizardPayment(array $in): array
    {
        $value = (float) ($in['product_value'] ?? 0);
        $paid = (float) ($in['paid_amount'] ?? 0);
        $status = $in['payment_status'] ?? null;
        PaymentConsistency::assert($status, $value, $paid);
        $pending = max(0, round($value - $paid, 2));
        $status = $status ?: ($paid <= 0 ? 'pending' : ($pending <= 0 ? 'paid' : 'partial'));
        return [$value, $paid, $pending, $status];
    }

    /**
     * Next PREFIX-NNNN number. Counts soft-deleted rows (they keep their unique
     * value) and locks the rows it reads, so two saves cannot take one number.
     * Call inside a DB transaction.
     */
    protected function nextNumber(string $modelClass, string $column, string $prefix): string
    {
        $query = method_exists($modelClass, 'bootSoftDeletes')
            ? $modelClass::withTrashed()
            : $modelClass::query();

        $last = $query->withoutGlobalScopes()
            ->where($column, 'like', $prefix . '%')
            ->orderByRaw("CAST(SUBSTRING_INDEX({$column}, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->value($column);

        $seq = ($last && preg_match('/-(\d+)$/', $last, $m)) ? ((int) $m[1] + 1) : 1;
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Use the requested number when it is free (or already this record's);
     * otherwise generate the next one.
     */
    protected function resolveNumber(string $modelClass, string $column, ?string $wanted, string $prefix, $existing = null): string
    {
        if ($existing && !empty($existing->{$column})) {
            return $existing->{$column};
        }
        $taken = $wanted && (method_exists($modelClass, 'bootSoftDeletes') ? $modelClass::withTrashed() : $modelClass::query())
            ->withoutGlobalScopes()->where($column, $wanted)->exists();
        return ($wanted && !$taken) ? $wanted : $this->nextNumber($modelClass, $column, $prefix);
    }

    /**
     * Log the real error, show the user a plain message (no SQL or paths).
     */
    protected function storeFailed(string $label, \Throwable $e, string $stepRoute, int $step)
    {
        if ($e instanceof ValidationException) {
            throw $e;
        }
        Log::error($label . ' save failed: ' . $e->getMessage(), ['exception' => $e]);
        return redirect()->route($stepRoute, $step)
            ->withInput()
            ->with('error', 'Could not save the ' . $label . '. Please check the details and try again. If it keeps failing, contact the administrator.');
    }
}
