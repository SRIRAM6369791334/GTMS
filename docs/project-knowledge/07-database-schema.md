# 07 — Database Schema

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Relationship map: [08-database-relationships.md](08-database-relationships.md).
>
> **Provenance of every fact below: REPOSITORY-DEFINED** (final state after applying all 72 migrations in `database/migrations`, in order). Nothing here was **verified against a live database**; no DB connection was made. Where code assumes something the migrations do not define, it is marked **INFERRED FROM CODE**. Where data migrations write rows, this is stated.
>
> Notation: `col type [null] [=default] {idx|uniq|FK→table (on delete)}`. Every table has `id` BIGINT PK unless stated. `ts` = `created_at`/`updated_at`. `sd` = `deleted_at` (soft delete).
> "Touched by" lists controllers / services that read (R) or write (C create, U update, D delete).

## A. Framework & auth tables
| Table | Columns | Touched by |
|---|---|---|
| users | name string; email string uniq; email_verified_at ts null; password string; **show_password string(255) null (plaintext)**; remember_token; image string null; mobile_num string(15) null; user_id BIGINT null FK→users (set null) = creator; user_code string null; role_id BIGINT null (no FK defined; `dropForeign` exists only in down()); branch_id BIGINT null (no FK); status tinyint =1 (1 active, 0 inactive); ts | AuthController R; UserController CRUD; BranchController D (cascade by code); all modules R |
| password_reset_tokens | email PK; token; created_at | unused |
| sessions | id string PK; user_id idx null; ip_address; user_agent; payload longtext; last_activity idx | framework (when SESSION_DRIVER=database) |
| cache, cache_locks | key PK; value / owner; expiration idx | framework |
| jobs, job_batches, failed_jobs | standard Laravel 11 schema | queue (database driver only); prod uses Redis |
| permissions | name, guard_name, ts; uniq(name, guard_name) | Spatie; RolesController R |
| roles | name, guard_name, **created_by FK→users null (set null)** (migration sets existing rows to 1), ts; uniq(name, guard_name) | RolesController CRUD |
| model_has_permissions, model_has_roles, role_has_permissions | Spatie pivots, cascade on delete | Spatie |
| notifications | id uuid PK; type; notifiable_type; notifiable_id; data **text** (JSON); read_at null; ts; idx(notifiable_type, notifiable_id) | NotificationController CRUD; MasterPipelineService C |

## B. Organisation & lookups
| Table | Columns | Touched by |
|---|---|---|
| branches | branch_name; contact_person null; mobile(15) null; address text null; city null; state null; country ='India'; pincode(10) null; status tinyint =1; ts | BranchController CRUD; every branch-scoped model |
| districts | name(100) uniq; code(10) uniq null; state ='Tamil Nadu'; status =1; ts | lookups everywhere |
| minerals | name(100) uniq; category enum(Major, Minor) ='Minor'; default_unit(20) ='CBM'; status =1; ts | lookups |
| lease_categories | code(50) uniq (e.g. "Rule 12", "Rule 19(1)", "MDCC"); name; land_type enum(Patta, Poramboke, Both) ='Patta'; status =1; is_custom bool =false; ts | CustomerController saveStep3 C (custom rule) |
| plan_types | name(100) uniq; status; ts | Mining |
| applicant_types | name(100) uniq; status; ts | Mining |
| nature_of_works | name; status =1; ts | Mining |
| modules | code(50) uniq (lease, mining, environment, ppt, dgps, drone); name; status; ts | folder resolution |
| folders | module_id FK→modules (cascade); name(100); sort_order =0; status =1; ts; uniq(module_id, name); idx(module_id, sort_order) | all document modules R |
| document_fields | folder_id FK→folders (cascade); nature_of_work_id FK→nature_of_works null (set null); name; required bool =true; sort_order; status; ts; idx(folder_id, sort_order) | slot generation |

*Data migration:* `2026_09_17_000001_seed_environment_clearance_master_data.php` inserts the `environment` module, its folders and its document fields when missing. **INFERRED FROM CODE:** `EnverionsoneController::ENV_MODULE_ID = 1` assumes the environment module has id 1, and `CustomerController@submit` assumes lease folders have ids 7/8/9 and document_fields ids 1–19.

## C. Customers
**customers** — customer_name; secondary_contact_person(255) null; company_name null; slug uniq null; mimas_no(50) null **uniq**; mimas_number(100) null; mimas_status(100) null; mobile_num(15); secondary_mobile_num(15) null; email null; parivesh_password(255) null (plaintext); email_password(255) null (plaintext); district_id FK null (set null); mineral_id FK null (set null); pan(10) **null** (was NOT NULL); aadhaar_no(20) null **uniq**; gstin(15) null; area **string(50)** null (was decimal); address text null; status tinyint =1; user_id FK→users null (unused portal link); created_by FK→users null; branch_id FK→branches null (set null); ts; sd. Indexes: customer_name, company_name, mobile_num, pan, (district_id, status).
*Data migrations:* placeholder `mimas_no` (`TN-MMS-CUST-####`) and Aadhaar (`9876-5432-####`) were written into **every pre-existing row** when those columns were added; `branch_id` was back-filled with the first branch.
*Note:* the DB-level unique index on `mimas_no` / `aadhaar_no` includes soft-deleted rows, while validation ignores them (`Rule::unique(...)->whereNull('deleted_at')`). Re-using the number of a deleted customer therefore passes validation but fails at the DB (INFERRED).
Touched by: CustomerDirectoryController CRUD; CustomerController C/U/restore; EnverionsoneController C/U; every module R.

## D. Lease module
**lease_applications** — common_id(50) null idx; application_no(50) uniq; customer_id FK (restrict); district_id FK (restrict); category_id FK→lease_categories (restrict); other_category_rule(255) null; mineral_id FK (restrict); other_mineral_name(255) null; taluk null; village null; area_extent_ha **string(50)** null; area_extent_acres(100) null; product_value / paid_amount / pending_amount decimal(12,2) null =0; payment_status enum(pending, partial, paid) ='pending'; start_date / end_date date null (idx end_date); lease_period_years int null; contact_person null; secondary_contact_person null; contact_mobile(15) null; secondary_contact_mobile(15) null; current_step tinyint =1; **status VARCHAR(100) ='draft'** (was enum); status_notes text null; go_number(100) null; go_date null; go_file null; rejection_note text null; assigned_inspector_id FK→users null; branch_id FK null; created_by FK null; master_project_id FK→master_projects null (set null); ts; sd. Indexes (district_id, status), (customer_id, status), (branch_id, status).
**lease_application_minerals** — lease_application_id FK (cascade); mineral_id FK (cascade); ts; uniq(lease_application_id, mineral_id).
**lease_survey_numbers** — lease_application_id FK (cascade); survey_no(100) idx; extent_ha decimal(10,4) null; classification(100) null; pattadar_name null; ts. (No writer found in code.)
**mimas_credentials** — lease_application_id FK (cascade); user_id string; password text (Laravel `encrypted` cast); email null; contact_number(15) null; mimas_ack_no(100) null; ack_date null; portal_status(50) ='pending'; ts.
**lease_documents** — lease_application_id FK (cascade); folder_id FK (cascade); document_field_id FK null (set null); document_name; file_name null; file_path null; file_type **string(255)** null; file_size BIGINT null; status enum(pending, uploaded, validated, approved, revision_required) ='pending'; review_note null; reviewed_by FK null; reviewed_at null; uploaded_by FK null; uploaded_at null; ts; sd; idx(lease_application_id, folder_id, status).
Touched by: CustomerController (all), MasterPipelineService, CustomerTracking R, PaymentCollection U (money), dashboard R.

## E. Mining module
**mining_applications** — common_id(50) null idx; nature_of_work_id FK null; other_nature_of_work_name null; application_no(50) uniq; customer_id FK (restrict); lease_application_id FK null; parent_plan_id FK→self null; applicant_type_id FK null; district_id FK (restrict); mineral_id FK **null**; other_mineral_name null; plan_type_id FK **null**; taluk, village null; survey_numbers_text text null; area_extent_ha string(50) null; product_value / paid / pending decimal, payment_status enum; start_date, end_date (idx); validity_years int =5; stage enum('6.1'…'6.6') ='6.1'; current_step tinyint =1; **status VARCHAR(100) ='draft'**; status_notes null; rqp_name, rqp_reg_no null; safety_distance_meters decimal(8,2) null; assigned_inspector_id FK null; approval_order_no, approval_date, approval_file null; kml_file_path null; branch_id FK null; created_by FK null; master_project_id FK null; ts; sd. Indexes (district_id, stage, status), (customer_id, status), (branch_id, status).
**mining_application_minerals** — pivot, uniq(mining_application_id, mineral_id).
**mining_documents** — same shape as lease_documents (FK mining_application_id).
**mining_boundary_points** — mining_application_id FK; pillar_id(50); latitude / longitude decimal(11,8); elevation null; remarks null. *Unused.*
**mining_production_schedules** — mining_application_id FK; year_number tinyint; production_target decimal(12,2); waste_removal null; uniq(app, year). *Unused.*

## F. Environment, PPT, EC
**environment_projects** — common_id null idx; project_code(50) uniq (`ENV-B1|B2-YYYY-NNNN`); customer_id FK (restrict); mining_application_id FK null; lease_application_id FK null; category enum(B1, B2) ='B2'; **sub_category VARCHAR(50) null** (TOR, ETA; legacy SC1 / SC2 migrated); b1_stage(50) null ='sc1_prep'; ppt_stage_1_id / ppt_stage_2_id BIGINT null (no FK); project_name; district_id FK (restrict); location null; contact_name, secondary_contact_person null; contact_phone(20) null; secondary_phone(25) null; contact_email null; parivesh_password / email_password (plaintext) null; public_hearing_date, public_hearing_minutes_file null; money columns (added by the 2026_09_23 migration); **status VARCHAR(100) ='draft'**; status_notes null; branch_id, created_by FK; master_project_id FK null; ts; sd. Indexes (district_id, category, status), (customer_id, status).
**environment_documents** — like lease_documents (FK environment_project_id).
**ppt_applications** — common_id null; application_no(50) uniq; customer_id FK (restrict); environment_project_id FK null; presentation_stage(50) ='tor_presentation' (also final_ec_presentation, b2_presentation); project_name; district_id FK (restrict); taluk_village null; mineral_id FK (restrict); parivesh_password / email_password null; **status VARCHAR(100) ='draft'**; status_notes; rqp_attending, company_rep_attending null; rep_mobile(15), rep_secondary_mobile(25) null; money columns; branch_id, created_by; master_project_id; ts; sd.
**ppt_agendas** — ppt_application_id FK; committee_type enum(SEAC, SEIAA); meeting_no; item_no; meeting_date idx; agenda_pdf; mom_pdf; outcome enum(Recommended, Query_EDS, Query_ADS, Rejected) null. *Unused.*
**ppt_documents** — like lease_documents without reviewed_at / uploaded_at.
**ec_certificates** — ec_ref_no(100) uniq; environment_project_id FK (**cascade**); customer_id FK (restrict); lease_application_id FK null; parivesh_app_no(100) null; parivesh_password / email_password null; applicant_name; issue_date; expiry_date (idx); validity_years null; communication_type enum(Grant, Rejection, ToR) ='Grant'; certificate_file null; conditions_summary null; **status VARCHAR(100) ='active'**; status_notes; money columns; created_by; ts; sd. (No branch_id.)
**ec_compliances** — compliance_no(50) uniq; customer_id FK; primary/secondary contact + phones; environment_project_id FK null; environment_project_name null; ec_certificate_id FK null; ec_certificate_file, ec_certificate_name null; project_name; district_id FK; taluk_village; mineral_id FK null; compliance_period(100) idx; compliance_year(10) ='2026'; submission_due_date, submission_date; parivesh_app_no, parivesh_acknowledgement_no, parivesh_uploaded_date; nabl_lab_name, nabl_certificate_no; monitoring_date; **status VARCHAR(100) ='draft'** (designed values: draft, documents_collected, lab_analysed, report_prepared, uploaded_to_parivesh, completed, archived); status_notes; product_value / paid / pending NOT NULL =0; payment_status enum; payment_notes; branch_id, created_by; ts; sd.
**ec_compliance_documents** — ec_compliance_id FK (cascade); folder_category enum(documents, site_analysis, report, parivesh_upload); document_name; file_*; is_mandatory bool =true; is_custom bool =false; status enum(pending, uploaded, verified, rejected); review_note; uploaded_by; ts; sd.

## G. Surveys
**dgps_surveys** — common_id; survey_no(50) uniq; field_book_no(100) null; customer_id FK (restrict); lease_application_id, mining_application_id FK null; lease_area_ha, surveyed_area_ha, area_discrepancy_ha decimal null; location; survey_date idx; surveyor_user_id FK→users null; survey_team_notes; instrument_model, instrument_serial_no; **survey_status VARCHAR(100) ='scheduled'**; status_notes; report_status enum(pending, verified, dispatched); gtm_report_file, autocad_dwg_file; money columns; branch_id, created_by; master_project_id; ts; sd.
**dgps_points** — dgps_survey_id FK; pillar_no; latitude / longitude; elevation. *Unused.*
**dgps_documents** — dgps_survey_id FK; folder_id FK; document_name; file_path; status(30) ='pending'; ts (+file_type expanded by migration).
**drone_surveys** — common_id; survey_no uniq; customer_id FK; lease / mining FK null; lease_area; location; flight_date idx; drone_pilot_name; pilot_rpc_no; drone_uin_no; drone_model; altitude_meters; gsd_cm_px; extracted_volume_cbm decimal(14,2); **survey_status VARCHAR(100) ='scheduled'** (designed: scheduled, flying_completed, processing, deliverables_ready, report_signed); status_notes; deliverable_files_path text; gtms_report_file; money columns; branch_id, created_by; master_project_id; ts; sd.
**drone_documents** — drone_survey_id FK; folder_id FK; document_name; file_path; file_size; status(30).

## H. Cross-module & accounts
**application_handlers** — application_type(50) idx null; application_id idx null; handlerable_type / handlerable_id (nullable morphs); name; role (free text); notes; sort_order =0; ts.
**application_payments** — application_type idx; application_id idx; payable_* morphs; product_value / paid_amount / pending_amount decimal(12,2) =0; payment_status enum(pending, partial, paid); notes; ts.
**activity_logs** — loggable_type(100) null; loggable_id null; user_id FK null; action(100); description; ip_address(45); user_agent; old_values / new_values JSON null; created_at =CURRENT_TIMESTAMP (no updated_at). Indexes (loggable_type, loggable_id), user_id, created_at.
**archived_activity_logs** — same + archived_at. *Unused.*
**project_flows** — flowable_type / id; step_code(20); step_name; status enum(pending, in_progress, passed, rejected); note; handled_by FK; handled_at; uniq(flowable_type, flowable_id, step_code). *Unused.*
**master_projects** — master_application_id(50) uniq; customer_id, branch_id, district_id FK null; taluk; village; survey_numbers text; area_extent_ha string; mineral_name; lease_application_id, dgps_survey_id, drone_survey_id, mining_application_id, environment_project_id, ppt_application_id FK null (set null); current_stage string ='lease' idx; pipeline_status string ='in_progress' idx; created_by FK; ts; sd; idx(current_stage, pipeline_status), branch_id, customer_id.
**pipeline_movements** — master_project_id FK (cascade); from_department(50); to_department(50); action_type(50) idx; priority(20) ='normal' idx; mistake_category(100) null; reason text; rectification_notes text; officer_id FK null; officer_name(150); metadata JSON; ts; idx(master_project_id, created_at), (from_department, to_department).
**quotations** — quotation_number(50) uniq + idx; customer_id FK (restrict); lease_application_id FK null; customer_name, company_name, subject, phone, email, gst_number, address, quarry_name; district_id FK null; taluk; village; survey_numbers; area_extent_ha decimal(10,4); mineral_name; scope_sections JSON; subtotal; tax_rate decimal(5,2) =18.00; tax_amount; total_amount; validity_days =30; payment_terms; milestones JSON; exclusions; authorized_signatory(255) default = consultancy principal; bank_account_id FK→bank_accounts null (set null); notes; status enum(draft, sent, accepted, rejected, converted) ='draft'; branch_id, created_by; ts; sd; idx(customer_id, status).
**quotation_items** — quotation_id FK (cascade) idx; service_name; sac_code(50); description; quantity decimal(10,2) =1; unit(50) ='Nos'; unit_rate; subtotal; ts.
**payment_receipts** — receipt_number(50) uniq; customer_id FK (restrict); quotation_id FK null (set null); application_type(50); application_id; amount_paid; balance_due; previous_paid; payment_mode enum(Cash, Cheque, NEFT/RTGS, UPI/GPay, Other) ='Cash'; bank_name; reference_number; transaction_date date idx; notes; branch_id, created_by; ts; sd; idx(customer_id, transaction_date), (application_type, application_id).
**bank_accounts** — account_name; account_number uniq; bank_name; branch_name; ifsc_code(20); is_default bool; is_active bool =true; created_by BIGINT (no FK); ts. *Data migration inserts the consultancy's default account and links all existing quotations to it.*
**chunked_uploads** — upload_token(64) uniq; user_id idx null; file_name; original_name; mime_type(255) null; total_size BIGINT; chunk_size int; total_chunks int; uploaded_chunks =0; target_module idx null; reference_id idx null; file_path; file_hash(64); checksum_sha256(64); status enum(pending, uploading, assembled, processing, completed, failed, cancelled) idx; error_message; metadata JSON; ts.
**chunked_upload_parts** — chunked_upload_id FK (cascade); chunk_index int; chunk_size; chunk_path; chunk_hash(64); is_uploaded bool =true; ts; uniq(chunked_upload_id, chunk_index).

## I. Legacy / unrelated
**categories** (cat_code uniq, cat_name, delete_status), **products** (bar_code uniq null, pro_name, gst, cast_per, mrp, unit, qty, discount_1..3, cat_id FK cascade, branch_id FK cascade, delete_status), **product_stocks** (product_id FK cascade, total / available / sale stock), **units** (units(100) uniq, delete_status), **environmental_projects / environmental_documents / environmental_activities** (August 2026 B2 prototype; unused), **mineral_stockpiles / mineral_stock_entries / mineral_dispatches** (unused).

## J. Schema-level observations
1. Status columns are free text for lease, mining, environment, PPT, EC certificate, EC compliance, DGPS and drone. The enum values listed above are the *original design*, not a DB constraint.
2. `users.role_id` and `users.branch_id` have no FK constraint; deleting a role or branch can orphan users (BranchController deletes users explicitly).
3. `ec_certificates.environment_project_id` cascades on delete (hard delete only; soft deletes do not cascade).
4. Money is duplicated: each application table has `product_value / paid_amount / pending_amount / payment_status`, mirrored in `application_payments` and summarised in `payment_receipts`. There is no single source of truth (see [12-business-logic.md](12-business-logic.md) R-PAY).
5. `MODIFY COLUMN` statements make the migrations MySQL-only.
