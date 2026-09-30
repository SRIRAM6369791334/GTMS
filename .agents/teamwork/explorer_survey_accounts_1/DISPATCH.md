# Dispatch — explorer_survey_accounts_1

## Task Description
Perform an in-depth codebase survey focusing on the database schema, models, and existing payment structures in GTMS for the new Accounts & Financial Management Module.

* Input:
- Authoritative User Request: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
- Project Root: c:\xampp\htdocs\GTMS\gtms
- Relevant codebase locations: `database/migrations`, `app/Models`, specifically inspecting:
  * Existing statutory application models & tables: `lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `ec_certificates`
  * Existing payment structure: `application_payments` table & `ApplicationPayment` model
  * Customer & Concession models & tables: `customers`, `customer_quarry_concessions` (and any related concession/mineral tables)
  * Existing financial columns in each statutory table (`product_value`, `paid_amount`, `pending_amount`, `payment_status`, etc.)

* Expected Output:
- Comprehensive survey report written to: `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1\survey_report.md`
- Detailed findings covering:
  1. Exact schema of the 7 statutory tables (columns, types, foreign keys, nullable attributes, financial columns).
  2. Exact schema of `application_payments` table and its polymorphic or direct relationships.
  3. Customer and quarry concession schema and relationships to statutory applications.
  4. Database requirements for new tables needed for the Accounts module: Quotations (`quotations`, `quotation_items`), Payments / Money Receipts (`payment_receipts` or additions to `application_payments`), and how they tie into existing models.
  5. Foreign key constraints, cascade rules, and potential data integrity risks.

* Constraints:
- Read-only exploration. DO NOT edit or create any source code or test files.
- Write your report strictly in your own directory: `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1\`.
- You MUST read `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md` first.

* Validation Criteria:
- Survey report provides exact table schemas, column names, model relationships, and schema recommendations for R1-R6.
- Report includes clear evidence citations from actual files.
