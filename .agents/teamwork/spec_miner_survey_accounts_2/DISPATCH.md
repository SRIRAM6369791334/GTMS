# Dispatch — spec_miner_survey_accounts_2

## Task Description
Perform an in-depth specification and code survey focusing on existing controllers, route definitions, Spatie RBAC permissions, and existing payment handling business logic in GTMS.

* Input:
- Authoritative User Request: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
- Project Root: c:\xampp\htdocs\GTMS\gtms
- Relevant codebase locations:
  * `routes/web.php`
  * `app/Http/Controllers/` (especially any existing controllers handling applications or payments like `ApplicationController`, `PaymentController`, `CustomerController`, etc.)
  * `database/seeders/` (Spatie role and permission seeders, e.g. `RolePermissionSeeder`, `PermissionTableSeeder`, `DatabaseSeeder`)
  * `app/Http/Middleware/` and auth guards

* Expected Output:
- Comprehensive specification and route survey report written to: `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\survey_report.md`
- Detailed findings covering:
  1. Current route naming conventions, middleware groups, URL prefixes, and parameter conventions in `routes/web.php`.
  2. How existing controllers handle payment recording, status updating, and atomic transactions.
  3. Spatie RBAC setup: Existing roles (Admin, Super Admin, Staff, etc.), permission naming convention (`module.action`), how permissions are seeded and checked in controllers/views.
  4. Required permissions for the Accounts module: `account.view`, `account.create`, `account.edit`, `account.delete` and any role assignments needed.
  5. API / AJAX endpoints needed for dynamic customer/concession selection, auto-populating metadata, pending application resolver across the 7 statutory modules.
  6. Controller design and action specifications for R1 (Quotations), R2 (Payment Collection), R3 (Receipt Vouchers), R4 (Customer Ledger), R5 (Reports & Export).

* Constraints:
- Read-only exploration. DO NOT edit or create any source code or test files.
- Write your report strictly in your own directory: `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\`.
- You MUST read `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md` first.

* Validation Criteria:
- Survey report provides precise route mappings, controller architecture, Spatie permission seeding details, and controller logic requirements for R1-R6.
32: - Report includes clear evidence citations from actual files.
33: 
34: ## 2026-09-29T05:36:06Z
35: You are spec_miner_survey_accounts_2.
36: Your working directory is: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2
37: Your task assignment is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\DISPATCH.md
38: Authoritative User Request is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
39: 
40: * Input:
41: - Read c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md first.
42: - Read c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\DISPATCH.md for complete instructions.
43: - Inspect `routes/web.php`, `app/Http/Controllers/`, `database/seeders/`, and Spatie RBAC setup.
44: 
45: * Expected Output:
46: - Write a comprehensive specification report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\survey_report.md` detailing:
47:   1. Route architecture, prefixing, naming conventions, and controller conventions in `routes/web.php`.
48:   2. How existing controllers handle payment recording and atomic transactions.
49:   3. Spatie RBAC setup: roles, permissions table, seeder classes, and exact permission names needed (`account.view`, `account.create`, `account.edit`, `account.delete`).
50:   4. Required endpoints (CRUD & AJAX) for Quotations, Payment Collection, Receipt Vouchers, Customer Ledger, Reports & CSV export.
51: - Write your completion handoff to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\handoff.md`.
52: - Send a completion message via send_message to orchestrator_3.
53: 
54: * Constraints:
55: - Read-only exploration. DO NOT create or edit source code, tests, or database files.
56: - All files written MUST stay inside `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\`.
57: 
58: * Validation Criteria:
59: - Report provides exact controller methods, route list, permission names, and business logic flow for R1-R6 with code citations.
