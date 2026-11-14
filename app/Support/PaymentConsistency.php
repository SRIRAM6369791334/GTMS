<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

/**
 * Checks that a chosen payment status agrees with the amounts entered.
 * Shared by Lease Step 7 and the Mining wizard payment step.
 * "free" is not checked here: its amounts are stored as 0 by the caller.
 */
class PaymentConsistency
{
    /**
     * @throws ValidationException with field errors (shown under each field)
     */
    public static function assert(?string $status, float $value, float $paid): void
    {
        if ($status === 'free') {
            return;
        }

        $errors = [];
        $pending = round($value - $paid, 2);

        if ($pending < 0) {
            $errors['paid_amount'] = 'Paid amount cannot be more than the product value.';
        } elseif ($status === 'paid' && $pending > 0) {
            $errors['payment_status'] = 'Status "Paid" needs the full product value paid. Choose "Partial".';
        } elseif ($status === 'pending' && $paid > 0) {
            $errors['payment_status'] = 'Status "Pending" cannot have a paid amount. Choose "Partial" or "Paid".';
        } elseif ($status === 'partial' && ($paid <= 0 || $pending <= 0)) {
            $errors['payment_status'] = 'Status "Partial" needs a paid amount that is more than 0 and less than the product value.';
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}
