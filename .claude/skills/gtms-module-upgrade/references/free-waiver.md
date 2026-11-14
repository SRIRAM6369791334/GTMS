# "Free — fee waived" payment option (pattern)

Built for lease applications. Owner decisions: Free ⇒ pending 0 **and** invoice 0; every user may select it; a reason is **required** and kept forever (shown even after completion).

## Data model (migration `2026_11_14_000001_add_free_waiver_to_lease_payments.php`)
- `payment_status` ENUM gets `'free'` on the module table **and** `application_payments`.
- New columns on the module table: `waived_amount decimal(12,2) null` (quoted value), `waiver_reason text null`, `waived_by bigint null`, `waived_at timestamp null`.
- Model: fillable + casts (`waived_amount` decimal:2, `waived_at` datetime), `isFree()`, `waivedByUser()` belongsTo User.

Key idea: a free application stores **product_value = paid = pending = 0** and keeps the quote in `waived_amount`. Existing calculations everywhere (`pending = value − paid` fallbacks in Accounts, ledgers, receipts, reports) then show nothing due without changes.

## Save (controller `saveStepN`)
```php
'payment_status' => 'nullable|string|in:paid,partial,pending,free',
'waiver_reason'  => 'required_if:payment_status,free|nullable|string|min:5|max:1000',
```
If free: `waived_amount = product_value input`, values 0, keep the original `waived_by/waived_at` when already free, else `Auth::id()/now()`. Log activity: `fee_waived`, `fee_waiver_updated` (reason changed), `fee_waiver_removed`. Mirror to `ApplicationPayment::updateOrCreate`. Carry `waived_amount`/`waiver_reason` through every place that rebuilds the session draft (step load, preview, resume).

## Step view
- Option `<option value="free">Free — fee waived (no charge)</option>`.
- Waiver box (`#s7_waiver`, violet dashed) with required textarea `name="waiver_reason"`, counter, `.waiver_reason_error` span; hidden unless free.
- Free mode JS: paid `readOnly` (not `disabled`, so it is submitted) = 0, presets disabled, pending 0, contract KPI label "Quoted · Waived", status box/pill violet via `data-status="free"`, focus the reason. Amount edits must not auto-switch status away from free; leaving free re-runs the auto status.
- On load when free, show `waived_amount` in the value input.
- Form `novalidate`; JS checks reason (≥5 chars) and shows the error under the field.

## Everywhere else
- Shared partial `resources/views/pages/lease_application/partials/fee_waiver_note.blade.php` (inline styles so PDFs render): amount waived, reason, by, when.
- Review step, dossier (Chapter VI badge, struck-through quote, note, copy-summary line), PDF report (KPI "Free · ₹0 Due", status badge, note).
- Invoices (`CustomerTrackingController` catalog): `is_free`, amount 0 (never the `150000` fallback), `waiver_note` under the item, "Free" tag in the picker.
- Accounts: `PaymentCollectionController` pending count `!in_array($status, ['paid','free'], true)`; `PaymentReceiptController` keeps `free`; JS badges in `accounts/payments/create` and `accounts/receipts/index` show "Free (Waived)".

## To replicate on another module
Same migration shape for that table, same controller block in its payment save, same view pieces, then add its catalog entry in `CustomerTrackingController` and its loop in `PaymentCollectionController` (each module has its own block). Test with a rolled-back transaction: dossier, report, invoice, Accounts dues.
