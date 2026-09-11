# GTMS — இறுதி டேட்டாபேஸ் ஸ்கீமா & ரிலேஷனல் ப்ளூபிரிண்ட் (Final Relational Database Blueprint)

**Granite / Mining Tracking Management System (GTMS)**
**பதிப்பு:** 2.0 (Production Architecture) · **தேதி:** 2026-09-04
**ஆவண நிலை:** 165 வணிக மற்றும் செயல்திறன் தேவைகள் அடிப்படையில் இறுதி செய்யப்பட்டது (100% Confirmed by User)

---

## 1. கட்டமைப்பின் முதன்மை அம்சங்கள் (Executive Architecture Highlights)

1. **மாட்யூல் வாரியான தனித்தனி ஆவண அட்டவணைகள் (Decoupled Module Documents):**
   - `lease_documents`, `mining_documents`, `environment_documents`, `ppt_documents`, `survey_documents` என தனித்தனியாகப் பிரிக்கப்பட்டுள்ளது. இது 1000+ பயனர்கள் ஒரே நேரத்தில் கோப்புகளை அப்லோட் செய்யும் போது டேபிள் லாக்கிங் (Table Lock Contention) சிக்கலை 100% தவிர்க்கும்.
2. **குத்தகை சர்வே எண்கள் துணை அட்டவணை (1:N Lease Survey Numbers):**
   - ஒரு குத்தகைக்கு பல சர்வே எண்கள் வரக்கூடும் என்பதால் `lease_survey_numbers` தனி அட்டவணையாக (SF எண், பரப்பளவு, வகைப்பாடு) 3NF விதிகளின்படி பிரிக்கப்பட்டுள்ளது.
3. **MIMAS நற்சான்றுகளுக்கான பிரத்யேக 1:N அட்டவணை:**
   - ஒரு குத்தகைக்கு பல MIMAS அக்கவுண்ட்கள் (`mimas_credentials`) பாதுகாப்பாக என்க்ரிப்ட் செய்யப்பட்ட பாஸ்வேர்டுகளுடன் சேமிக்கப்படும்.
4. **கனிம இருப்பு & ராயல்டி மேலாண்மை (Granite / Mineral Stockpile):**
   - பழைய கடையின் சரக்கு இருப்புக்கு பதிலாக, குவாரியில் வெட்டப்பட்ட கிரானைட்/கற்கள் உற்பத்தி வரவு (`mineral_stock_entries`) மற்றும் லாரிகளில் ஏற்றி அனுப்பப்பட்ட அளவு (`mineral_dispatches`), சீக்னோரேஜ் கட்டணம் மற்றும் ஆண்டு குத்தகை ஒதுக்கீட்டுக் கணக்கீடு (Annual Quota).
5. **அதிவேக செயல்திறன் & 1000 பயனர்கள் முடங்காமல் இயங்கும் கட்டமைப்பு (High Scale & Concurrency):**
   - Redis Cache (Lookups & Sessions), Composite Indexing, Optimistic + Pessimistic Row-level Locking, Background Queue Workers (Chunked Uploads & PDF Generation), மற்றும் Multi-branch Isolation (`BranchScope`).

---

## 2. என்டிட்டி உறவு வரைபடம் (Entity Relationship Diagram - ERD)

```
       ┌────────────────────────────────────────────────────────┐
       │                 ACCESS & MASTER LOOKUPS                │
       │  branches ──< users ──< activity_logs                  │
       │  roles >──< permissions (via role_permission)          │
       │  districts, minerals, lease_categories, plan_types...  │
       └───────────────────────────┬────────────────────────────┘
                                   │ FK References
     ┌─────────────────────────────┼─────────────────────────────┐
     │                             │                             │
CUSTOMERS                     CORE MODULES                 CHECKLIST MASTERS
     │                             │                      (folders, document_fields)
     ├──< lease_applications       │                             │
     │       ├──< lease_surveys    │                             │
     │       ├──< mimas_creds      │                             ▼
     │       └──< lease_docs ──────┼─────────────────────────────┘
     │                             │
     ├──< mining_applications      │
     │       ├──< boundary_points  │
     │       ├──< production_sched │
     │       └──< mining_docs ─────┼─────────────────────────────┐
     │                             │                             │
     ├──< environment_projects     │                             │
     │       ├──< ec_certificates │                             │
     │       └──< env_docs ────────┼─────────────────────────────┤
     │                             │                             │
     ├──< ppt_applications         │                             │
     │       ├──< ppt_agendas      │                             │
     │       └──< ppt_docs ────────┼─────────────────────────────┤
     │                             │                             │
     ├──< dgps_surveys             │                             │
     │       ├──< dgps_points      │                             │
     │       └──< dgps_docs ───────┼─────────────────────────────┤
     │                             │                             │
     └──< drone_surveys            │                             │
             └──< drone_docs ──────┴─────────────────────────────┘
                                   │
                   MINERAL STOCKPILE & TRANSACTIONS
                   mineral_stockpiles ──< mineral_stock_entries
                                      ──< mineral_dispatches
```

---

## 3. முழுமையான அட்டவணை விவரங்கள் & உறவுகள் (Complete Table Specifications)

### 3.1 பயனர்கள், பொறுப்புகள் & கிளைகள் (Identity & RBAC)

#### 1. `branches` (அலுவலக கிளைகள் / துறைகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `branch_name` — varchar(255), Unique, Not Null
* `branch_code` — varchar(50), Unique, Nullable
* `contact_person` — varchar(255), Not Null
* `mobile` — varchar(15), Not Null
* `email` — varchar(255), Nullable
* `address` — text, Not Null
* `city` — varchar(100), Nullable
* `state` — varchar(100), Default 'Tamil Nadu'
* `pincode` — varchar(10), Nullable
* `status` — tinyInteger, Default 1 (1=Active, 0=Inactive)
* `created_at`, `updated_at`, `deleted_at` (Soft Delete)

#### 2. `roles` (டைனமிக் பயனர் பொறுப்புகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `name` — varchar(100), Unique, Not Null (Super Admin, District Officer, Surveyor, Inspector...)
* `display_name` — varchar(255), Not Null
* `description` — text, Nullable
* `created_at`, `updated_at`

#### 3. `permissions` (அனுமதிகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `module` — varchar(50), Not Null (lease, mining, environment, ppt, dgps, drone, users, stock)
* `name` — varchar(100), Unique, Not Null (`mining.view`, `mining.approve`, `lease.create`...)
* `display_name` — varchar(255), Not Null
* `created_at`, `updated_at`

#### 4. `role_permission` (N:M Pivot Table)
* `id` — unsignedBigInteger, PK, Auto Increment
* `role_id` — unsignedBigInteger, FK → `roles.id`, Cascade on Delete
* `permission_id` — unsignedBigInteger, FK → `permissions.id`, Cascade on Delete
* `UQ: (role_id, permission_id)`

#### 5. `users` (பயனர்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `name` — varchar(255), Not Null
* `email` — varchar(255), Unique, Not Null
* `mobile_num` — varchar(15), Unique, Not Null
* `role_id` — unsignedBigInteger, FK → `roles.id`, Restrict on Delete
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Nullable, Set Null on Delete
* `image` — varchar(255), Nullable (Path: `public/uploads/users/...`)
* `password` — varchar(255), Not Null (bcrypt)
* `show_password` — varchar(255), Nullable (அவசரத் தேவைக்கான அட்மின் ஃபீல்டு)
* `status` — tinyInteger, Default 1 (1=Active, 0=Inactive)
* `remember_token` — varchar(100), Nullable
* `created_at`, `updated_at`, `deleted_at` (Soft Delete)
* **Indexes:** `email`, `mobile_num`, `role_id`, `branch_id`, `status`

---

### 3.2 முதன்மை மாஸ்டர் அட்டவணைகள் (Master Lookups)

#### 6. `districts` (38 தமிழ்நாடு மாவட்டங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `name` — varchar(100), Unique, Not Null
* `code` — varchar(10), Unique, Nullable (MDU, CBE, CHN...)
* `state` — varchar(100), Default 'Tamil Nadu'
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`

#### 7. `minerals` (கனிமங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `name` — varchar(100), Unique, Not Null (Rough Stone, Granite, Limestone, Gravel, Quartz...)
* `category` — enum('Major', 'Minor'), Default 'Minor'
* `default_unit` — varchar(20), Default 'CBM' ('CBM' அல்லது 'Tonnes')
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`

#### 8. `lease_categories` (குத்தகை விதிகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `code` — varchar(50), Unique, Not Null (Rule 12, Rule 19(1), Rule 19-A, Rule 36-F, MDCC...)
* `name` — varchar(255), Not Null
* `land_type` — enum('Patta', 'Poramboke', 'Both'), Default 'Patta'
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`

#### 9. `plan_types` (சுரங்க திட்ட வகைகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `name` — varchar(100), Unique, Not Null (Mining Plan, Revised Mining Plan, Modified Plan, Scheme of Mining)
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`

#### 10. `applicant_types` (விண்ணப்பதாரர் வகைகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `name` — varchar(100), Unique, Not Null (Individual, Partnership, Pvt Ltd, Public Ltd, Trust)
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`

#### 11. `modules` (கணினி தொகுதிகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `code` — varchar(50), Unique, Not Null (lease, mining, env_b1, env_b2, ec, ppt, dgps, drone)
* `name` — varchar(100), Not Null
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`

#### 12. `folders` (கோப்புறைகள் - Module Keyed)
* `id` — unsignedBigInteger, PK, Auto Increment
* `module_id` — unsignedBigInteger, FK → `modules.id`, Cascade on Delete
* `name` — varchar(100), Not Null
* `sort_order` — integer, Default 0
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`
* `UQ: (module_id, name)`

#### 13. `document_fields` (ஆவண சரிபார்ப்பு பட்டியல் மாஸ்டர்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `folder_id` — unsignedBigInteger, FK → `folders.id`, Cascade on Delete
* `name` — varchar(255), Not Null (தேவையான ஆவணத்தின் பெயர்)
* `required` — boolean, Default true
* `sort_order` — integer, Default 0
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`

---

### 3.3 வாடிக்கையாளர் மேலாண்மை (Customer Directory)

#### 14. `customers` (மைய வாடிக்கையாளர் அட்டவணை)
* `id` — unsignedBigInteger, PK, Auto Increment
* `customer_name` — varchar(255), Not Null (பிரதிநிதி பெயர்)
* `company_name` — varchar(255), Nullable (நிறுவன பெயர் / குவாரி பெயர்)
* `mobile_num` — varchar(15), Not Null
* `email` — varchar(255), Nullable
* `district_id` — unsignedBigInteger, FK → `districts.id`, Nullable
* `mineral_id` — unsignedBigInteger, FK → `minerals.id`, Nullable
* `pan` — varchar(10), Not Null
* `gstin` — varchar(15), Nullable
* `area` — decimal(10,2), Nullable (குவாரி பரப்பளவு Ha)
* `address` — text, Nullable
* `status` — tinyInteger, Default 1 (1=Active, 0=Inactive)
* `user_id` — unsignedBigInteger, FK → `users.id`, Nullable (எதிர்கால போர்ட்டல் லாகின் இணைப்பு)
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at` (Soft Delete)
* **Indexes:** `customer_name`, `company_name`, `mobile_num`, `pan`, `district_id`, `status`

---

### 3.4 குத்தகை விண்ணப்பம் & MIMAS (Lease Application)

#### 15. `lease_applications` (குத்தகை விண்ணப்பம்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `application_no` — varchar(50), Unique, Not Null (தானாக உருவான எண் / கோப்பு எண்)
* `customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `district_id` — unsignedBigInteger, FK → `districts.id`, Restrict on Delete
* `category_id` — unsignedBigInteger, FK → `lease_categories.id`, Restrict on Delete
* `mineral_id` — unsignedBigInteger, FK → `minerals.id`, Restrict on Delete
* `taluk` — varchar(255), Nullable
* `village` — varchar(255), Nullable
* `area_extent_ha` — decimal(10,2), Nullable (பரப்பளவு ஹெக்டேரில்)
* `area_extent_acres` — varchar(100), Nullable (ஏக்கர் & சென்ட்)
* `start_date` — date, Nullable
* `end_date` — date, Nullable
* `lease_period_years` — integer, Nullable
* `contact_person` — varchar(255), Nullable
* `contact_mobile` — varchar(15), Nullable
* `current_step` — tinyInteger, Default 1 (Wizard Step 1 to 7)
* `status` — enum('draft', 'submitted', 'under_scrutiny', 'approved', 'rejected', 'expired'), Default 'draft'
* `go_number` — varchar(100), Nullable (அரசு ஆணை எண்)
* `go_date` — date, Nullable
* `go_file` — varchar(255), Nullable (ஆணை நகல் PDF)
* `rejection_note` — text, Nullable
* `assigned_inspector_id` — unsignedBigInteger, FK → `users.id`, Nullable
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Nullable
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at` (Soft Delete)
* **Composite Indexes:** `(district_id, status)`, `(customer_id, status)`

#### 16. `lease_survey_numbers` (1:N Lease Survey Numbers)
* `id` — unsignedBigInteger, PK, Auto Increment
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Cascade on Delete
* `survey_no` — varchar(100), Not Null (எ.கா: `142/1A`)
* `extent_ha` — decimal(10,4), Nullable
* `classification` — varchar(100), Nullable (Dry Patta, Wet, Govt Poramboke...)
* `pattadar_name` — varchar(255), Nullable
* `created_at`, `updated_at`

#### 17. `mimas_credentials` (1:N MIMAS Portal Logins)
* `id` — unsignedBigInteger, PK, Auto Increment
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Cascade on Delete
* `user_id` — varchar(255), Not Null
* `password` — text, Not Null (Encrypted AES-256 via Laravel Crypt)
* `email` — varchar(255), Nullable
* `contact_number` — varchar(15), Nullable
* `mimas_ack_no` — varchar(100), Nullable
* `ack_date` — date, Nullable
* `portal_status` — varchar(50), Default 'pending'
* `created_at`, `updated_at`

#### 18. `lease_documents` (குத்தகை ஆவணங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Cascade on Delete
* `folder_id` — unsignedBigInteger, FK → `folders.id`, Cascade on Delete
* `document_field_id` — unsignedBigInteger, FK → `document_fields.id`, Nullable
* `document_name` — varchar(255), Not Null
* `file_name` — varchar(255), Nullable (அசல் கோப்பு பெயர்)
* `file_path` — varchar(255), Nullable (டிஸ்க்கில் உள்ள பாதை)
* `file_type` — varchar(20), Nullable (pdf, dwg, jpg...)
* `file_size` — unsignedBigInteger, Nullable (bytes)
* `status` — enum('pending', 'uploaded', 'validated', 'approved', 'revision_required'), Default 'pending'
* `review_note` — text, Nullable
* `reviewed_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `reviewed_at` — timestamp, Nullable
* `uploaded_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `uploaded_at` — timestamp, Nullable
* `created_at`, `updated_at`, `deleted_at`
* **Composite Indexes:** `(lease_application_id, folder_id, status)`

---

### 3.5 சுரங்கத் திட்டம் (Mining Applications)

#### 19. `mining_applications` (சுரங்கத் திட்ட விண்ணப்பம்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `application_no` — varchar(50), Unique, Not Null (எ.கா: `GTMS/MP/2026/001`)
* `customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Nullable (விருப்ப இணைப்பு)
* `parent_plan_id` — unsignedBigInteger, FK → `mining_applications.id`, Nullable (திருத்தப்பட்ட திட்ட இணைப்பு)
* `applicant_type_id` — unsignedBigInteger, FK → `applicant_types.id`, Nullable
* `district_id` — unsignedBigInteger, FK → `districts.id`, Restrict on Delete
* `mineral_id` — unsignedBigInteger, FK → `minerals.id`, Restrict on Delete
* `plan_type_id` — unsignedBigInteger, FK → `plan_types.id`, Restrict on Delete
* `taluk` — varchar(255), Nullable
* `village` — varchar(255), Nullable
* `survey_numbers_text` — text, Nullable
* `area_extent_ha` — decimal(10,2), Nullable
* `start_date` — date, Nullable
* `end_date` — date, Nullable
* `validity_years` — integer, Default 5
* `stage` — enum('6.1', '6.2', '6.3', '6.4', '6.5', '6.6'), Default '6.1'
* `status` — enum('draft', 'scrutiny', 'inspection', 'presentation', 'approved', 'rejected', 'archived'), Default 'draft'
* `rqp_name` — varchar(255), Nullable
* `rqp_reg_no` — varchar(100), Nullable
* `safety_distance_meters` — decimal(8,2), Nullable (நீர்நிலை/சாலை இடைவெளி)
* `assigned_inspector_id` — unsignedBigInteger, FK → `users.id`, Nullable
* `approval_order_no` — varchar(100), Nullable
* `approval_date` — date, Nullable
* `approval_file` — varchar(255), Nullable
* `kml_file_path` — varchar(255), Nullable (எல்லை KML கோப்பு)
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Nullable
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at`
* **Composite Indexes:** `(district_id, stage, status)`

#### 20. `mining_boundary_points` (எல்லை தூண் ஆயத்தொலைவுகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `mining_application_id` — unsignedBigInteger, FK → `mining_applications.id`, Cascade on Delete
* `pillar_id` — varchar(50), Not Null (P1, P2, P3...)
* `latitude` — decimal(11,8), Not Null
* `longitude` — decimal(11,8), Not Null
* `elevation` — decimal(8,2), Nullable
* `remarks` — varchar(255), Nullable
* `created_at`, `updated_at`

#### 21. `mining_production_schedules` (ஆண்டு உற்பத்தி அட்டவணை)
* `id` — unsignedBigInteger, PK, Auto Increment
* `mining_application_id` — unsignedBigInteger, FK → `mining_applications.id`, Cascade on Delete
* `year_number` — tinyInteger, Not Null (1, 2, 3, 4, 5)
* `production_target` — decimal(12,2), Not Null (CBM / Tonnes)
* `waste_removal` — decimal(12,2), Nullable
* `created_at`, `updated_at`
* `UQ: (mining_application_id, year_number)`

#### 22. `mining_documents` (சுரங்கத் திட்ட ஆவணங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `mining_application_id` — unsignedBigInteger, FK → `mining_applications.id`, Cascade on Delete
* `folder_id` — unsignedBigInteger, FK → `folders.id`, Cascade on Delete
* `document_field_id` — unsignedBigInteger, FK → `document_fields.id`, Nullable
* `document_name` — varchar(255), Not Null
* `file_name` — varchar(255), Nullable
* `file_path` — varchar(255), Nullable
* `file_type` — varchar(20), Nullable
* `file_size` — unsignedBigInteger, Nullable
* `status` — enum('pending', 'uploaded', 'validated', 'approved', 'revision_required'), Default 'pending'
* `review_note` — text, Nullable
* `reviewed_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `reviewed_at` — timestamp, Nullable
* `uploaded_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `uploaded_at` — timestamp, Nullable
* `created_at`, `updated_at`, `deleted_at`
* **Composite Indexes:** `(mining_application_id, folder_id, status)`

---

### 3.6 சுற்றுச்சூழல் அனுமதி (Environmental Clearance B1 / B2)

#### 23. `environment_projects` (சுற்றுச்சூழல் திட்டங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `project_code` — varchar(50), Unique, Not Null (எ.கா: `ENV-B2-2026-0001`)
* `customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `mining_application_id` — unsignedBigInteger, FK → `mining_applications.id`, Nullable (FK Link)
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Nullable
* `category` — enum('B1', 'B2'), Default 'B2'
* `project_name` — varchar(255), Not Null
* `district_id` — unsignedBigInteger, FK → `districts.id`, Restrict on Delete
* `location` — varchar(255), Nullable
* `contact_name` — varchar(255), Nullable
* `contact_phone` — varchar(20), Nullable
* `contact_email` — varchar(255), Nullable
* `public_hearing_date` — date, Nullable (B1-க்கு மட்டும்)
* `public_hearing_minutes_file` — varchar(255), Nullable
* `status` — enum('draft', 'validation', 'approved', 'reported', 'archived'), Default 'draft'
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Nullable
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at`
* **Composite Indexes:** `(district_id, category, status)`

#### 24. `environment_documents` (சுற்றுச்சூழல் ஆவணங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `environment_project_id` — unsignedBigInteger, FK → `environment_projects.id`, Cascade on Delete
* `folder_id` — unsignedBigInteger, FK → `folders.id`, Cascade on Delete
* `document_field_id` — unsignedBigInteger, FK → `document_fields.id`, Nullable
* `document_name` — varchar(255), Not Null
* `file_name` — varchar(255), Nullable
* `file_path` — varchar(255), Nullable
* `file_type` — varchar(20), Nullable
* `file_size` — unsignedBigInteger, Nullable
* `status` — enum('pending', 'uploaded', 'validated', 'approved', 'revision_required'), Default 'pending'
* `review_note` — text, Nullable
* `reviewed_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `reviewed_at` — timestamp, Nullable
* `uploaded_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `uploaded_at` — timestamp, Nullable
* `created_at`, `updated_at`, `deleted_at`
* **Composite Indexes:** `(environment_project_id, folder_id, status)`

---

### 3.7 சுற்றுச்சூழல் சான்றிதழ் (EC Certificates)

#### 25. `ec_certificates` (சுற்றுச்சூழல் அனுமதி சான்றிதழ்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `ec_ref_no` — varchar(100), Unique, Not Null (சான்றிதழ் எண்)
* `environment_project_id` — unsignedBigInteger, FK → `environment_projects.id`, Cascade on Delete
* `customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Nullable
* `parivesh_app_no` — varchar(100), Nullable (பரிவேஷ் போர்டல் எண்)
* `applicant_name` — varchar(255), Not Null
* `issue_date` — date, Not Null
* `expiry_date` — date, Not Null
* `validity_years` — integer, Nullable
* `communication_type` — enum('Grant', 'Rejection', 'ToR'), Default 'Grant'
* `certificate_file` — varchar(255), Nullable (சான்றிதழ் PDF கோப்பு)
* `conditions_summary` — text, Nullable (நிபந்தனைகள் குறிப்பு)
* `status` — enum('active', 'expired', 'surrendered', 'revoked'), Default 'active'
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at`
* **Indexes:** `ec_ref_no`, `environment_project_id`, `expiry_date`, `status`

---

### 3.8 பிபிடி துறை (PPT Department)

#### 26. `ppt_applications` (SEIAA விளக்கக்காட்சி விண்ணப்பம்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `application_no` — varchar(50), Unique, Not Null
* `customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `environment_project_id` — unsignedBigInteger, FK → `environment_projects.id`, Nullable
* `project_name` — varchar(255), Not Null
* `district_id` — unsignedBigInteger, FK → `districts.id`, Restrict on Delete
* `taluk_village` — varchar(255), Nullable
* `mineral_id` — unsignedBigInteger, FK → `minerals.id`, Restrict on Delete
* `status` — enum('draft', 'agenda_scheduled', 'presented', 'approved', 'rejected', 'archived'), Default 'draft'
* `rqp_attending` — varchar(255), Nullable
* `company_rep_attending` — varchar(255), Nullable
* `rep_mobile` — varchar(15), Nullable
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Nullable
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at`

#### 27. `ppt_agendas` (SEAC / SEIAA கூட்ட நிகழ்ச்சி நிரல்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `ppt_application_id` — unsignedBigInteger, FK → `ppt_applications.id`, Cascade on Delete
* `committee_type` — enum('SEAC', 'SEIAA'), Default 'SEAC'
* `meeting_no` — varchar(50), Not Null
* `item_no` — varchar(50), Not Null
* `meeting_date` — date, Not Null
* `agenda_pdf` — varchar(255), Nullable
* `mom_pdf` — varchar(255), Nullable (Minutes of Meeting PDF)
* `outcome` — enum('Recommended', 'Query_EDS', 'Query_ADS', 'Rejected'), Nullable
* `created_at`, `updated_at`

#### 28. `ppt_documents` (பிபிடி ஆவணங்கள் - 11 கோப்புறைகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `ppt_application_id` — unsignedBigInteger, FK → `ppt_applications.id`, Cascade on Delete
* `folder_id` — unsignedBigInteger, FK → `folders.id`, Cascade on Delete
* `document_field_id` — unsignedBigInteger, FK → `document_fields.id`, Nullable
* `document_name` — varchar(255), Not Null
* `file_name` — varchar(255), Nullable
* `file_path` — varchar(255), Nullable
* `file_type` — varchar(20), Nullable
* `file_size` — unsignedBigInteger, Nullable
* `status` — enum('pending', 'uploaded', 'validated', 'approved', 'revision_required'), Default 'pending'
* `review_note` — text, Nullable
* `reviewed_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `uploaded_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at`
* **Composite Indexes:** `(ppt_application_id, folder_id, status)`

---

### 3.9 டிஜிபிஎஸ் கள ஆய்வு (DGPS Survey)

#### 29. `dgps_surveys` (DGPS கள ஆய்வு)
* `id` — unsignedBigInteger, PK, Auto Increment
* `survey_no` — varchar(50), Unique, Not Null (தானாக உருவான எண்: `DGPS-2026-0001`)
* `field_book_no` — varchar(100), Nullable (கள ஆய்வுக் குழுவின் எண்)
* `customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Nullable
* `mining_application_id` — unsignedBigInteger, FK → `mining_applications.id`, Nullable
* `lease_area_ha` — decimal(10,2), Nullable (குத்தகை ஆவண பரப்பளவு)
* `surveyed_area_ha` — decimal(10,2), Nullable (சர்வேயில் அளந்த உண்மையான பரப்பளவு)
* `area_discrepancy_ha` — decimal(10,2), Nullable (வித்தியாசம்)
* `location` — varchar(255), Nullable
* `survey_date` — date, Nullable
* `surveyor_user_id` — unsignedBigInteger, FK → `users.id`, Nullable (சர்வேயர்)
* `survey_team_notes` — text, Nullable
* `instrument_model` — varchar(100), Nullable (Trimble, Leica...)
* `instrument_serial_no` — varchar(100), Nullable
* `survey_status` — enum('scheduled', 'in_progress', 'completed', 'cancelled'), Default 'scheduled'
* `report_status` — enum('pending', 'verified', 'dispatched'), Default 'pending'
* `gtm_report_file` — varchar(255), Nullable (இறுதி GTM அறிக்கை PDF)
* `autocad_dwg_file` — varchar(255), Nullable (வரைபடம் DWG)
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Nullable
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at`

#### 30. `dgps_points` (GCP எல்லை தூண் ஆயத்தொலைவுகள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `dgps_survey_id` — unsignedBigInteger, FK → `dgps_surveys.id`, Cascade on Delete
* `pillar_no` — varchar(50), Not Null
* `latitude` — decimal(11,8), Not Null
* `longitude` — decimal(11,8), Not Null
* `elevation` — decimal(8,2), Nullable
* `created_at`, `updated_at`

#### 31. `dgps_documents` (DGPS ஆவணங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `dgps_survey_id` — unsignedBigInteger, FK → `dgps_surveys.id`, Cascade on Delete
* `folder_id` — unsignedBigInteger, FK → `folders.id`, Cascade on Delete
* `document_name` — varchar(255), Not Null
* `file_path` — varchar(255), Nullable
* `status` — varchar(30), Default 'pending'
* `created_at`, `updated_at`

---

### 3.10 ட்ரோன் வான்வழி ஆய்வு (Drone Survey)

#### 32. `drone_surveys` (ட்ரோன் ஆய்வு)
* `id` — unsignedBigInteger, PK, Auto Increment
* `survey_no` — varchar(50), Unique, Not Null (தானாக உருவான எண்: `DRONE-2026-0001`)
* `customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Nullable
* `mining_application_id` — unsignedBigInteger, FK → `mining_applications.id`, Nullable
* `lease_area` — decimal(10,2), Nullable
* `location` — varchar(255), Nullable
* `flight_date` — date, Nullable
* `drone_pilot_name` — varchar(255), Nullable
* `pilot_rpc_no` — varchar(100), Nullable (DGCA உரிம எண்)
* `drone_uin_no` — varchar(100), Nullable (DGCA UIN)
* `drone_model` — varchar(100), Nullable
* `altitude_meters` — decimal(8,2), Nullable
* `gsd_cm_px` — decimal(6,2), Nullable
* `extracted_volume_cbm` — decimal(14,2), Nullable (வெட்டப்பட்ட கனிம கன அளவு)
* `survey_status` — enum('scheduled', 'flying_completed', 'processing', 'deliverables_ready', 'report_signed'), Default 'scheduled'
* `deliverable_files_path` — text, Nullable (Orthomosaic, Contour DXF, 3D Mesh)
* `gtms_report_file` — varchar(255), Nullable (PDF அறிக்கை)
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Nullable
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`, `deleted_at`

#### 33. `drone_documents` (ட்ரோன் ஆவணங்கள்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `drone_survey_id` — unsignedBigInteger, FK → `drone_surveys.id`, Cascade on Delete
* `folder_id` — unsignedBigInteger, FK → `folders.id`, Cascade on Delete
* `document_name` — varchar(255), Not Null
* `file_path` — varchar(255), Nullable
* `file_size` — unsignedBigInteger, Nullable
* `status` — varchar(30), Default 'pending'
* `created_at`, `updated_at`

---

### 3.11 கனிம இருப்பு & ராயல்டி மேலாண்மை (Quarry Mineral Stockpile)

#### 34. `mineral_stockpiles` (குவாரி கனிம இருப்பு தளம்)
* `id` — unsignedBigInteger, PK, Auto Increment
* `quarry_customer_id` — unsignedBigInteger, FK → `customers.id`, Restrict on Delete
* `lease_application_id` — unsignedBigInteger, FK → `lease_applications.id`, Restrict on Delete
* `mineral_id` — unsignedBigInteger, FK → `minerals.id`, Restrict on Delete
* `branch_id` — unsignedBigInteger, FK → `branches.id`, Restrict on Delete
* `annual_permitted_quota` — decimal(14,2), Not Null (அனுமதிக்கப்பட்ட ஆண்டு ஒதுக்கீடு)
* `current_stock_cbm` — decimal(14,2), Default 0.00 (தற்போதைய இருப்பு)
* `total_dispatched_cbm` — decimal(14,2), Default 0.00 (வெளியேற்றப்பட்ட அளவு)
* `unit` — varchar(20), Default 'CBM'
* `status` — tinyInteger, Default 1
* `created_at`, `updated_at`
* `UQ: (lease_application_id, mineral_id)`

#### 35. `mineral_stock_entries` (உற்பத்தி வரவு - Stock In)
* `id` — unsignedBigInteger, PK, Auto Increment
* `mineral_stockpile_id` — unsignedBigInteger, FK → `mineral_stockpiles.id`, Cascade on Delete
* `entry_date` — date, Not Null
* `quantity` — decimal(12,2), Not Null
* `source_type` — enum('quarry_extraction', 'drone_volume_audit', 'manual_adjustment'), Default 'quarry_extraction'
* `verified_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `remarks` — text, Nullable
* `created_at`, `updated_at`

#### 36. `mineral_dispatches` (வெளியேற்றம் / ராயல்டி வசூல் - Stock Out)
* `id` — unsignedBigInteger, PK, Auto Increment
* `mineral_stockpile_id` — unsignedBigInteger, FK → `mineral_stockpiles.id`, Cascade on Delete
* `dispatch_date` — timestamp, Not Null
* `quantity` — decimal(12,2), Not Null
* `vehicle_number` — varchar(50), Not Null
* `driver_name` — varchar(100), Nullable
* `destination` — varchar(255), Nullable
* `seigniorage_fee_inr` — decimal(12,2), Default 0.00 (அரசு கட்டணம்)
* `challan_no` — varchar(100), Nullable
* `status` — enum('pending', 'approved', 'dispatched', 'cancelled'), Default 'dispatched'
* `created_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `created_at`, `updated_at`

---

### 3.12 பணிப்பாய்வு, தணிக்கை & அறிவிப்புகள் (Workflow, Audit & Notifications)

#### 37. `project_flows` (படிநிலை சரிபார்ப்பு பதிவுகள் - Stages 6.1 - 6.6)
* `id` — unsignedBigInteger, PK, Auto Increment
* `flowable_type` — varchar(100), Not Null (எ.கா: `App\Models\MiningApplication`)
* `flowable_id` — unsignedBigInteger, Not Null
* `step_code` — varchar(20), Not Null (6.1, 6.2, 6.3...)
* `step_name` — varchar(100), Not Null
* `status` — enum('pending', 'in_progress', 'passed', 'rejected'), Default 'pending'
* `note` — text, Nullable
* `handled_by` — unsignedBigInteger, FK → `users.id`, Nullable
* `handled_at` — timestamp, Nullable
* `created_at`, `updated_at`
* `UQ: (flowable_type, flowable_id, step_code)`

#### 38. `activity_logs` (முழுமையான அழியாத தணிக்கைப் பதிவு - Immutable Audit Trail)
* `id` — unsignedBigInteger, PK, Auto Increment
* `loggable_type` — varchar(100), Nullable
* `loggable_id` — unsignedBigInteger, Nullable
* `user_id` — unsignedBigInteger, FK → `users.id`, Nullable
* `action` — varchar(100), Not Null (எ.கா: `document_uploaded`, `status_changed`, `resubmitted`)
* `description` — text, Nullable
* `ip_address` — varchar(45), Nullable (IPv4 / IPv6)
* `user_agent` — text, Nullable
* `old_values` — json, Nullable
* `new_values` — json, Nullable
* `created_at` — timestamp, Not Null
* **Indexes:** `(loggable_type, loggable_id)`, `user_id`, `created_at`

#### 39. `archived_activity_logs` (1 வருடத்திற்கு முந்தைய பழைய தணிக்கைக் காப்பகம்)
* `activity_logs` போன்ற அதே நெடுவரிசைகள் (ஆண்டு வாரியாக பழைய பதிவுகளை நகர்த்தி மெயின் டேபிளை வேகமாக வைத்திருக்க).

#### 40. `notifications` (Laravel Database Notifications)
* `id` — char(36), PK (UUID)
* `type` — varchar(255), Not Null
* `notifiable_type` — varchar(255), Not Null
* `notifiable_id` — unsignedBigInteger, Not Null
* `data` — text, Not Null (JSON payload)
* `read_at` — timestamp, Nullable
* `created_at`, `updated_at`
* **Index:** `(notifiable_type, notifiable_id)`

---

## 4. அதிவேக செயல்திறன் & 1000 பயனர்களுக்கான உகப்பாக்க உத்திகள் (Performance Specifications)

1. **MySQL 8.0 InnoDB Configuration (`my.ini`):**
   ```ini
   max_connections = 500
   innodb_buffer_pool_size = 2G
   innodb_log_file_size = 512M
   innodb_flush_log_at_trx_commit = 2
   query_cache_type = 0
   slow_query_log = 1
   long_query_time = 1
   ```
2. **Redis In-Memory Caching (`.env`):**
   ```env
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```
   * *கேச் செய்யப்படும் தரவுகள்:* Districts (38), Minerals, Folders & Document Checklists, Roles & Permissions Matrix, Dashboard KPI Counters (5-10 நிமிட கேச்).
3. **50MB - 100MB ஆவணங்கள் அப்லோடு முறை (Chunking & Queuing):**
   * பிளேட் UI-ல் Dropzone.js அல்லது Resumable.js பயன்படுத்தி 2MB துண்டுகளாக (Chunks) ஏற்றுதல்.
   * அப்லோட் முடிந்தவுடன் கோப்பை இணைக்கும் பணி மற்றும் WebP இமேஜ் கம்ப்ரஷன் பின்னணி கியூ (Laravel Queue Worker) மூலம் நிகழும்.
4. **டெட்லாக் தவிர்ப்பு (Deadlock-Free Transactions):**
   * அனைத்து கண்ட்ரோலர்களிலும் ஒரே சீரான அட்டவணை வரிசையில் பரிவர்த்தனைகள் (`DB::transaction()`) + `retry(3)`.
5. **கிளை வாரியான தரவுப் பாதுகாப்பு (Multi-Branch Isolation):**
   * Eloquent Global Scope `BranchScope` மூலம் Super Admin அல்லாத சாதாரண பயனர்களின் குவெரிகளில் தானாகவே `WHERE branch_id = auth()->user()->branch_id` இணைக்கப்படும்.

---

## 5. மைக்ரேஷன்களை இயக்கும் சரியான வரிசைமுறை (Topological Migration Sequence)

| படி | மைக்ரேஷன் கோப்பின் பெயர் | உருவாக்கும் அட்டவணைகள் | சார்ந்திருக்கும் அட்டவணைகள் (Dependencies) |
|:---:|:---|:---|:---|
| 01 | `create_branches_table` | `branches` | — |
| 02 | `create_roles_table` | `roles` | — |
| 03 | `create_permissions_table` | `permissions` | — |
| 04 | `create_role_permission_table` | `role_permission` | `roles`, `permissions` |
| 05 | `create_users_table` | `users` | `roles`, `branches` |
| 06 | `create_districts_table` | `districts` | — |
| 07 | `create_minerals_table` | `minerals` | — |
| 08 | `create_lease_categories_table` | `lease_categories` | — |
| 09 | `create_plan_types_table` | `plan_types` | — |
| 10 | `create_applicant_types_table` | `applicant_types` | — |
| 11 | `create_modules_table` | `modules` | — |
| 12 | `create_folders_table` | `folders` | `modules` |
| 13 | `create_document_fields_table` | `document_fields` | `folders` |
| 14 | `create_customers_table` | `customers` | `districts`, `minerals`, `users` |
| 15 | `create_lease_applications_table` | `lease_applications` | `customers`, `districts`, `lease_categories`, `minerals` |
| 16 | `create_lease_survey_numbers_table` | `lease_survey_numbers` | `lease_applications` |
| 17 | `create_mimas_credentials_table` | `mimas_credentials` | `lease_applications` |
| 18 | `create_lease_documents_table` | `lease_documents` | `lease_applications`, `folders`, `document_fields` |
| 19 | `create_mining_applications_table` | `mining_applications` | `customers`, `lease_applications`, `districts`, `minerals` |
| 20 | `create_mining_boundary_points_table` | `mining_boundary_points` | `mining_applications` |
| 21 | `create_mining_production_schedules_table` | `mining_production_schedules` | `mining_applications` |
| 22 | `create_mining_documents_table` | `mining_documents` | `mining_applications`, `folders`, `document_fields` |
| 23 | `create_environment_projects_table` | `environment_projects` | `customers`, `mining_applications`, `districts` |
| 24 | `create_environment_documents_table` | `environment_documents` | `environment_projects`, `folders`, `document_fields` |
| 25 | `create_ec_certificates_table` | `ec_certificates` | `environment_projects`, `customers` |
| 26 | `create_ppt_applications_table` | `ppt_applications` | `customers`, `districts`, `minerals` |
| 27 | `create_ppt_agendas_table` | `ppt_agendas` | `ppt_applications` |
| 28 | `create_ppt_documents_table` | `ppt_documents` | `ppt_applications`, `folders` |
| 29 | `create_dgps_surveys_table` | `dgps_surveys` | `customers`, `users` |
| 30 | `create_dgps_points_table` | `dgps_points` | `dgps_surveys` |
| 31 | `create_dgps_documents_table` | `dgps_documents` | `dgps_surveys`, `folders` |
| 32 | `create_drone_surveys_table` | `drone_surveys` | `customers` |
| 33 | `create_drone_documents_table` | `drone_documents` | `drone_surveys`, `folders` |
| 34 | `create_mineral_stockpiles_table` | `mineral_stockpiles` | `customers`, `lease_applications`, `minerals` |
| 35 | `create_mineral_stock_entries_table` | `mineral_stock_entries` | `mineral_stockpiles` |
| 36 | `create_mineral_dispatches_table` | `mineral_dispatches` | `mineral_stockpiles` |
| 37 | `create_project_flows_table` | `project_flows` | `users` |
| 38 | `create_activity_logs_table` | `activity_logs` | `users` |
| 39 | `create_archived_activity_logs_table` | `archived_activity_logs` | — |
| 40 | `create_notifications_table` | `notifications` | — |

---
*இந்த முழுமையான இறுதி டேட்டாபேஸ் ஸ்கீமா மற்றும் ரிலேஷனல் ப்ளூபிரிண்ட் `C:\xampp\htdocs\GTMS\gtms\docs\database-analysis\GTMS_FINAL_DATABASE_SCHEMA_BLUEPRINT.md` என்ற கோப்பில் நிரந்தரமாக சேமிக்கப்பட்டுள்ளது.*
