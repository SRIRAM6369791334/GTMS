# Verification protocol

## Server
- Worktree preview: `.claude/launch.json` config `gtms-worktree` → `php artisan serve --port=8001`; start with the browser `preview_start` tool. Port 8000 is the user's main checkout.
- MySQL (XAMPP) must be running. If it crashed with "Aria recovery failed": move `C:\xampp\mysql\data\aria_log*` to a backup folder (not delete) and start `mysqld.exe --defaults-file=…/my.ini --standalone`.
- Login: seeded admin from `database/seeders/RolePermissionSeeder.php` (local dev only; don't print the password in chat).
- After view edits: `php artisan view:clear`; before finishing: `php artisan view:cache` (compiles every Blade file) then `view:clear`.
- `php -l` every changed PHP file; `node --check` every changed JS file.

## Data safety
- Never create/delete records without asking. If test data is approved: label it (`UI TEST … (delete me)`), keep status inactive where possible, list ids, and give the cleanup SQL; delete only when asked (check linked rows first via `information_schema.COLUMNS`).
- Prefer a **rolled-back transaction** for server-side checks: bootstrap Laravel in a script, `DB::beginTransaction()`, update/render, assert, `DB::rollBack()`, then confirm the row is unchanged.
- Don't POST real saves from the browser on the user's drafts; stub `fetch`/`$.ajax`/`window.open`/download clicks in page JS when testing buttons.

## Layout checks (JS in the page)
```js
const r = e => e.getBoundingClientRect();
// same top for inputs in a row
['#a','#b','#c'].map(s => Math.round(r(document.querySelector(s)).top));
// equal card heights
[...document.querySelectorAll('.kpi')].map(e => Math.round(r(e).height));
// no horizontal scroll (run at 375px)
({w: innerWidth, sw: document.documentElement.scrollWidth});
// text fully visible
el.scrollWidth <= el.clientWidth + 1;
```
- Widths: 1440×950 desktop (`resize_window`), 375 mobile preset; reset to `desktop` when done.
- Dark mode: `document.body.setAttribute('data-theme-version','dark')`, screenshot, set back.
- Focus styles: use a real click (`computer left_click` with `ref`), then read `getComputedStyle`.

## Behaviour checks
- Every button and modal path: open, each action, cancel, Escape (real key), remembered choices, reset.
- Validation: type bad input with the `computer type` tool (one insert, like paste); submit empty with a real click; confirm no request was sent and errors are under fields; confirm no popup.
- Status-driven UI: drive every status (pending/partial/paid/free) and assert text, colours (`backgroundColor`), and values.
- Wait ≥250ms after state changes before reading colours (transitions).

## Report
State numbers measured, what passed, what could not be tested (and why), and that nothing is committed.
