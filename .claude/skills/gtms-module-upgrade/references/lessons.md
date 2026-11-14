# Lessons from the Lease module (bugs found → fix)

Check each page for every item. `scripts/survey.php` flags many automatically.

## Styling / layout

| Symptom | Cause | Fix |
|---|---|---|
| Global CSS "doesn't work" on a page | Page has 500–3500 lines of ID-scoped inline CSS (`#gtms-x-page .s1-…`) that outranks it | Map components to `lz-*` and strip the duplicate inline rules (scripts) |
| Spacing missing (`mb-1.5`, `gap-2.5`, `p-3.5`) | Bootstrap 5 has no half steps | Defined in `gtms-ui.css` §9 — nothing to do, just don't remove |
| Modal header white, white text/close invisible | `app.blade.php` sets `.modal-header{background:#F8FAFC!important}` | Use own classes (e.g. `gow-*`) for custom modal headers |
| Pink text/buttons | Theme `secondary` = pink | Slate tokens instead of `*-secondary` |
| Eyebrow navy on one step, orange on others | `text-primary` (`!important`) on the eyebrow | Remove `text-primary` |
| Long page title wraps under header | No ellipsis | `.header-left .dashboard_bar` ellipsis (gtms-ui §10) |
| Inputs in a row at different heights | A badge next to one label made it wrap (`justify-content-between`) | Badge inside label + `.lz-row .lz-label{min-height:20px}` |
| Eye/copy buttons shorter than input | Page CSS `height:40px` vs 44px inputs; global `.btn:hover` lift | `min-height:44px; align-self:stretch; transform:none!important` on addon buttons |
| Preset buttons ("100% Full") wrap to 2 lines | Free-width buttons in a narrow column | 3-col grid segmented control, short labels (`100% / 50% / Clear`) |
| Cards in a row unequal; footers misaligned | Link wrapper not full height / no flex column | `a{display:block;height:100%}` + card flex column + `margin-top:auto` |
| Values cut ("sriramsri@gmail.c…") | `text-truncate` | Wrap; clamp to 2 lines only for headline values |
| Review page cramped, labels wrap | 4-column page inside 900px `.lz-page` + inner `.wizard-wrap` 760/1040 cap | Widen page (1180px) and `> .wizard-wrap{max-width:100%}` |
| Table inputs squashed | 4 inputs in 700px | `min-width` on table inside `.table-responsive` |
| Pagination in grey box, huge buttons | Theme styles `.dataTables_paginate` | Override inside the page's footer container |
| `tr:hover{border-left}` does nothing | Separate border model ignores row borders | `td:first-child{box-shadow:inset 3px 0 0 …}` |
| Muted text unreadable | `#94A3B8` on white (2.6:1) | `#64748B` (4.8:1) |
| Grey-on-grey select ("Optional") | `text-muted bg-light` + theme | Explicit slate colours on that state |
| Icon box loses colour on focus | Focus rule set addon background to white | Tint (`#EFF6FF`) + blue icon on `:focus-within` |
| Whole page dims behind a small toast | Backdrop rule matched toast containers | Dim only non-toast containers (gtms-ui §7) |

## Behaviour

| Symptom | Cause | Fix |
|---|---|---|
| "Validation failed. Please check all required fields" although all filled | Server **500** (FK error) shown with generic text | Log first (`storage/logs/laravel.log`). Show real 422 field errors under fields; for 500 say "server could not save (error N)" |
| Step 1 save crashes: FK on `category_id` | `lease_categories` empty and code used `LeaseCategory::first() ?: 1` | Seed real master data (only the needed table, `updateOrInsert`), return a clear 422 when missing |
| Pasting "98ab7654321" gives "98765432" | `maxlength` cuts raw text before letters are filtered | No `maxlength`; script filters then caps (gtms-validate) |
| Native "Please fill out this field" bubbles; JS never runs | `required` + form without `novalidate` blocks the submit event | `novalidate` on the form, validate in JS |
| Validation errors as popups | SweetAlert/toastr on 422 | `GTMSValidate.showErrors(form, errors)`; remove `toastr.error(value[0])` loops |
| Edit-form errors appear twice | Spans named `.edit_<field>_error` | Validator looks for both `.x_error` and `.edit_x_error` |
| File downloads twice | `<a download>` default + JS download | `e.preventDefault()` in the click handler |
| Option text from file names injected as HTML | `innerHTML` with extension in titles | Build with `textContent` |
| "Open in VLC" only downloads | Browsers cannot launch desktop apps | "Tap to open" screen + "Always open files of this type" tip; never ship .bat/.vbs launchers |
| Invoice shows ₹1,50,000 for a free/zero item | `$amt = $val > 0 ? $val : 150000.00` | Explicit free flag; amount 0 + waiver note |
| Paid status counts as pending in Accounts | `if ($pending > 0 \|\| $status !== 'paid')` | `!in_array($status, ['paid','free'], true)` |
| Free app shows full amount due in Accounts | Fallback `pending = value − paid` when `pending_amount` is 0 | Store chargeable value 0, quoted value in `waived_amount` |
| Address saved into `taluk` | Old step-1 mapping (`'taluk' => $validated['address']`) | Known, not fixed yet — mention if touching step 1 |

## Process

- `preg_replace*` returns `null` on regex error → writing it empties the file. Guard every rewrite (the scripts do). Restore with `git checkout -- <file>` if it happens (only safe when the file had no other uncommitted edits).
- PHP `getopt()` stops at the first non-option argument — parse `$argv` manually.
- Quoting PHP inside bash `-r '…'` breaks on `'` / `$` / regex classes: write a script file instead.
- The preloader takes 3–7 s; wait before screenshots. Pane screenshots can be stale/partially painted: retake, or measure with JS.
- `document.hasFocus()` is false in a background pane — `:focus` styles only show after a real click (`computer` tool), not `el.focus()`.
- Scripted `.click()` on a submit button can skip native validation that a real click hits — test with a real click too.
