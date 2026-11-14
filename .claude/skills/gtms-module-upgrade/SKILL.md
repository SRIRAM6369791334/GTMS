---
name: gtms-module-upgrade
description: Upgrade a GTMS module (DGPS survey, drone survey, EC certificate, EC compliance, PPT, environment B1/B2, mining plan, accounts, customers…) to the standard proven on the Lease Application module — shared design system (gtms-ui/gtms-lease CSS, lz-* classes), field validation with errors under fields, SweetAlert confirms/toasts, Open-With file modal, alignment/spacing fixes, payment "Free / fee waived" option, and a strict verify-in-browser protocol. Use whenever the user asks to improve, fix, redesign, align, validate or add the free/waiver option to any GTMS page or module, or says "use the lease skill / same like lease application".
---

# GTMS module upgrade (Lease Application standard)

The Lease Application module (`/application`, `/step1`…`/step8`, `/viewapplication`, `/application/{id}/report`) is the **reference implementation**. Every other module should end up looking and behaving the same. This skill is the recipe, the shared building blocks, and the mistakes already paid for.

Read before starting:
- `references/design-system.md` — what already exists globally (load nothing twice), the `lz-*` class catalogue, tokens, colours.
- `references/lessons.md` — every bug/pitfall found on the lease module and its fix. **Scan it for each page.**
- `references/verification.md` — how to test without touching the user's data.
- `references/lease-module.md` — the reference module: pages, routes, modals, functions, what was changed.
- `references/free-waiver.md` — only when the module has a payment step and the user wants the Free option.

## Working agreement with this user

- Reply in **Tamil** (technical terms, code, paths in English). Keep it short; tables for change lists.
- **Errors go under the field. No popup** for validation (no SweetAlert/toastr for field errors). SweetAlert only for confirms (delete/irreversible) and short success toasts.
- Ask before writing to the database (test records, master data, deleting). Testing with a DB transaction + rollback is fine.
- Never claim "done" without checking in the browser (port 8001 worktree server) and saying what was and wasn't verified.
- The user tests on `http://127.0.0.1:8000` (main checkout). Changes in a worktree only show on `:8001` until merged — say so.
- Full-page screenshots repeat the fixed sidebar 3–4×. That is the capture tool, not a bug; confirm with `document.querySelectorAll('.dlabnav .metismenu > li').length`.

## Workflow (per module)

1. **Inventory.** List the module's routes (`php artisan route:list --method=GET`) → controller → blade view. Count inline CSS per view. Tell the user the file list (Tamil) and confirm scope before large changes.
2. **Survey each view** (read-only):
   `php .claude/skills/gtms-module-upgrade/scripts/survey.php resources/views/pages/<module>/<view>.blade.php`
   It prints the wrapper id, class prefixes and which components map to `lz-*`, plus pitfalls (pink `text-secondary`, `maxlength`, native `confirm()`, fake money fallbacks, `text-truncate`, 40px buttons…).
3. **Look at it** in the browser at 1440px and 375px before changing anything; note concrete problems (overlaps, wrapping labels, unequal cards, cut-off values, low contrast).
4. **Apply the shared design** (wizard/form pages):
   - Wrapper needs an id (`id="gtms-<module>-page"`); add one if missing.
   - Dry run, then apply:
     ```
     php .claude/skills/gtms-module-upgrade/scripts/apply_lz.php <view> --root=<wrapper-id> --prefix=<s1> --dry
     php .claude/skills/gtms-module-upgrade/scripts/apply_lz.php <view> --root=<wrapper-id> --prefix=<s1>
     php .claude/skills/gtms-module-upgrade/scripts/strip_rules.php <view> --root=<wrapper-id> --prefix=<s1>          # dry run
     php .claude/skills/gtms-module-upgrade/scripts/strip_rules.php <view> --root=<wrapper-id> --prefix=<s1> --write
     ```
     Repeat per prefix (`s1`, `s7`…). Use `--skip=input-group,input,select,addon` when the page's input groups are custom (no Bootstrap `.input-group`), as on lease steps 6–7.
   - `git diff` the view. Check nothing page-unique was removed.
5. **Fix page-specific problems** in the page's own `<style>` (scoped `#<wrapper-id> …`). Typical fixes are in `lessons.md` (equal-height cards, label min-height, segmented presets, wrap instead of truncate, widen review pages).
6. **Validation**: add `data-v="mobile|pan|gstin|aadhaar|name|company|code|text|email|address|pincode|decimal"` (+ `data-v-label`, `data-v-different`) to inputs, `data-validate novalidate` to the form. Make the server rules match (`references/design-system.md#validation`). For AJAX-saved forms call `GTMSValidate.validateForm(form)` before sending and `GTMSValidate.showErrors(form, json.errors)` on 422.
7. **Confirms / toasts**: `return confirm('…')` handlers convert automatically. Replace script `confirm()/alert()/toastr` with `GTMS.confirm(msg).then(ok => …)` / `GTMS.toast(icon, text)`.
8. **Payment step** (if any): consider the Free/waiver option — follow `references/free-waiver.md` end to end (DB, controller, view, dossier, report, invoice, Accounts).
9. **Verify** (`references/verification.md`): measure alignment with `getBoundingClientRect`, test every button/modal path, 375px no horizontal scroll, dark mode, `php artisan view:cache` compiles.
10. **Report** in Tamil: what changed (table), what was verified (with numbers), what was not, nothing committed unless asked.

## Hard rules

- Scripts that rewrite files must never write an empty/failed result (`preg_*` returns `null` on error — guard it). An empty invoice view happened once; restored with `git checkout -- <file>`.
- Don't add new fake fallbacks (`?? 50000`, `: 150000`, `LeaseCategory::first() ?: 1`). Return a clear error instead.
- Don't bump unrelated files. Cache-bust changed assets: `/css/gtms-ui.css?v=N`, `/js/gtms-validate.js?v=N`, `gtms-lease.css?v=N`.
- Keep element IDs used by JS. Grep the page's `<script>` for `getElementById`/`$('#…')` before renaming markup.
- Prefer CSS in the page's own scoped block or the shared files over inline `style=""`.
