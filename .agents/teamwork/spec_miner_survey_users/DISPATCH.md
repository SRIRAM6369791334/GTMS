## 2026-09-28T06:00:46Z
You are spec_miner_survey_users.
Your working directory is: C:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_users
Your parent is: orchestrator_2 (Conversation ID: 6b69e301-99cc-4206-b8c6-8af6297273f3)

[Prompt Quality Score: 10/10]

* **Input:**
  - Mandatory requirement document: C:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (Read section "## 2026-09-28T05:54:24Z" first)
  - Project root: C:\xampp\htdocs\GTMS\gtms
  - Relevant source files:
    - `app/Http/Controllers/UserController.php`
    - `app/Http/Controllers/AuthController.php` (or login controller)
    - `app/Models/User.php`
    - `routes/web.php` (all `/user` routes, login/logout routes, middleware)
    - `tests/Feature/ApplicationHandlersAndPaymentsTest.php`
    - `tests/Feature/PptDgpsAndEcComplianceTest.php`
    - `phpunit.xml`, `tests/TestCase.php`

* **Expected Output:**
  - Create a detailed, comprehensive report in `C:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_users\handoff.md`.
  - Include:
    1. Complete catalog of all User and Auth routes: URI, HTTP verb, controller action, middleware.
    2. Detailed audit of `AuthController`: login authentication logic — how dual-identifier login works (canonical email vs `user_code`, e.g. `LUK_001`), password hashing/checking, session handling, remember me, error messages.
    3. Detailed audit of `UserController`:
       - User provisioning: automatic `user_code` generation algorithm/pattern, required fields, validation rules.
       - Dual role synchronization: how `users.role_id` and Spatie `model_has_roles` are synchronized on create and update.
       - Avatar image uploading: directory path, file naming, mime validation, and how old avatars are unlinked/deleted on replacement or user deletion.
       - Deletion safeguards: self-deletion prevention (`Auth::id() === $id`), last-admin deletion block, cascading effects.
       - Permission middleware gating: all permissions guarding user management routes.
    4. Existing test harness baseline:
       - How `TestCase.php` is configured, traits used (e.g. `DatabaseTransactions`), how users and auth states are established in `ApplicationHandlersAndPaymentsTest.php` and `PptDgpsAndEcComplianceTest.php`.
       - Database connection and safety patterns to avoid collisions on unique keys (e.g. `email`, `user_code`, `aadhaar_no`).
    5. Blueprint for test cases needed to satisfy R3 (User Lifecycle & Dual RBAC) and ensuring zero regressions on existing test suites.

* **Constraints:**
  - You are READ-ONLY. Do NOT write, modify, or delete any source code files in `app/`, `routes/`, `resources/`, or `database/`.
  - Write ONLY within your working directory: `C:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_users/`.
  - Deliver your complete findings in `handoff.md` and send a message back to parent when done.

* **Validation Criteria:**
  - Exact code snippets, algorithms (e.g. user_code generation, dual-identifier login check), line numbers, and file paths.
  - Zero assumptions; verified against actual source files.
