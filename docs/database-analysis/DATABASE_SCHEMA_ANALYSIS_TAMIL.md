# GTMS — தரவுத்தள பகுப்பாய்வு மற்றும் ரிலேஷனல் ஸ்கீமா வடிவமைப்பு (Database Architecture Guide)

**Granite / Mining Tracking Management System (GTMS)**
**ஆய்வு அமைவிடம்:** `C:\xampp\htdocs\GTMS\gtms\docs\database-analysis`
**பதிப்பு:** 1.0 (Tamil Reference)

---

## 1. திட்டப் பின்னணி மற்றும் கோப்புகள் பகுப்பாய்வு (Overview & File Analysis)

`C:\xampp\htdocs\GTMS\gtms\docs\database-analysis` கோப்புறையில் மொத்தம் **17 ஆய்வு ஆவணங்கள்** உள்ளன. அவை மாவட்ட சுரங்க அலுவலகத்தின் பல்வேறு பணிப்பாய்வுகளை (Workflows) விரிவாக ஆராய்ந்து, ஒரு சீரான 3NF (Third Normal Form) ரிலேஷனல் டேட்டாபேஸை உருவாக்குவதற்காக ஆவணப்படுத்தப்பட்டுள்ளன.

### 17 ஆவணங்களின் பட்டியல் மற்றும் விளக்கம்:

| எண் | கோப்பின் பெயர் | முக்கிய உள்ளடக்கம் |
|:---:|:---|:---|
| 01 | `01-ui-inventory.md` | UI Blade டெம்ப்ளேட்டுகள், படிவங்கள், நேவிகேஷன் மற்றும் பக்கங்களின் முழு பட்டியல். |
| 02 | `02-page-and-form-analysis.md` | ஒவ்வொரு பக்கத்திலும் உள்ள படிவங்கள், பொத்தான்கள் மற்றும் செயல்பாடுகளின் பகுப்பாய்வு. |
| 03 | `03-complete-input-field-analysis.md` | UI-ல் உள்ள 300-க்கும் மேற்பட்ட இன்புட் ஃபீல்டுகளின் வகை மற்றும் விவரங்கள். |
| 04 | `04-data-classification.md` | தரவு வகைப்பாடு (Master Data, Transactional, Document, Log, Derived). |
| 05 | `05-entity-discovery-and-normalization.md` | என்டிட்டிகளை பிரித்தெடுத்து 3NF வரை இயல்பாக்கம் (Normalization) செய்தல். |
| 06 | `06-table-design.md` | பரிந்துரைக்கப்பட்ட 31 டேபிள்கள், அவற்றின் நெடுவரிசைகள் (Columns), டேட்டா வகைகள். |
| 07 | `07-relationship-diagram.md` | டேபிள்களுக்கு இடையேயான உறவுகள் (1:1, 1:N, N:M, Polymorphic ERD). |
| 08 | `08-master-and-lookup-tables.md` | மாவட்டங்கள், கனிமங்கள், விண்ணப்பதாரர் வகைகள் போன்ற மாஸ்டர் அட்டவணைகள். |
| 09 | `09-ui-to-database-mapping.md` | UI-ல் உள்ள ஒவ்வொரு இன்புட்டும் எந்த டேபிளில் சேமிக்கப்படும் என்ற மேப்பிங். |
| 10 | `10-data-flow.md` | திட்ட உருவாக்கம் முதல் இறுதி ஆவணக் காப்பகம் (Archival) வரையிலான டேட்டா ஓட்டம். |
| 11 | `11-indexing-strategy.md` | தேடுதல் வேகம் மற்றும் செயல்திறனை அதிகரிக்க தேவையான இண்டெக்ஸ் உத்திகள். |
| 12 | `12-migration-plan.md` | லாராவெல் (Laravel) மைக்ரேஷன்களை இயக்கும் சரியான வரிசைமுறை (Topological Order). |
| 13 | `13-model-plan.md` | லாராவெல் Eloquent மாடல்கள், அவற்றின் Fillable பண்புகள் மற்றும் உறவுகள். |
| 14 | `14-validation-plan.md` | படிவ உள்ளீடுகளுக்கான லாராவெல் வேலிடேஷன் (Validation Rules). |
| 15 | `15-data-dictionary.md` | முழு டேட்டா அகராதி (ஒவ்வொரு ஃபீல்டின் விளக்கம், Nullability). |
| 16 | `16-unknown-business-requirements.md` | வணிக ரீதியாக உறுதிப்படுத்தப்பட வேண்டிய 13 திறந்த கேள்விகள் (Gaps). |
| 17 | `DATABASE_ARCHITECTURE_MASTER.md` | ஒட்டுமொத்த டேட்டாபேஸ் கட்டமைப்பின் முதன்மை மேலோட்ட ஆவணம். |

---

## 2. மிக முக்கியமான 5 கட்டிடக்கலை முடிவுகள் (Key Architectural Decisions)

1. **பாலிமார்பிக் ஆவண அட்டவணை (`project_documents`):**
   - 7 வெவ்வேறு மாட்யூல்களுக்கும் தனித்தனி ஆவண டேபிள்கள் உருவாக்காமல், ஒரே ஒரு பாலிமார்பிக் அட்டவணையை (`documentable_type`, `documentable_id`) கொண்டு அனைத்து கோப்புகளையும் நிர்வகித்தல்.
2. **மத்திய வாடிக்கையாளர் அட்டவணை (`customers`):**
   - வாடிக்கையாளர் விவரங்கள் மீண்டும் மீண்டும் எழுதப்படாமல் ஒரே இடத்தில் சேமிக்கப்பட்டு `customer_id` Foreign Key மூலம் இணைக்கப்படுதல்.
3. **மாஸ்டர் டேபிள்கள் (Master Lookups):**
   - மாவட்டங்கள், கனிமங்கள், குத்தகை விதிகள் ஆகியவை ஸ்ட்ரிங் மதிப்புகளாக மீண்டும் வராமல் மாஸ்டர் ஐடிகள் மூலம் இயல்பாக்கம் செய்யப்பட்டுள்ளது.
4. **கணக்கிடப்படும் தரவுகளை சேமிக்காதிருத்தல் (No Derived Data in DB):**
   - ஆவணங்களின் முன்னேற்ற சதவீதம், டாஷ்போர்டு எண்ணிக்கைகள் டேட்டாபேஸில் சேமிக்கப்படாமல் இயக்க நேரத்தில் (Run-time) கணக்கிடப்படும்.
5. **பாலிமார்பிக் பணிப்பாய்வு & தணிக்கைப் பதிவு (`project_flows` & `activity_logs`):**
   - அனைத்து மாட்யூல்களின் சரிபார்ப்பு படிகளும் தணிக்கை விவரங்களும் மையப்படுத்தப்பட்ட பாலிமார்பிக் முறையில் இயங்கும்.

---

## 3. GTMS முழுமையான ரிலேஷனல் டேபிள் கட்டமைப்பு (Complete Relational Schema)

```
                            ┌────────────────────────────┐
                            │      ACCESS & MASTERS       │
                            │ users / roles / permissions │
                            │ departments / districts /   │
                            │ minerals / categories /     │
                            │ plan_types / applicant_types│
                            └───────────────┬─────────────┘
                                            │ FK references
       ┌────────────────────────────────────┼─────────────────────────────────────┐
       │                                    │                                     │
  CUSTOMERS                    ┌────────────▼────────────┐                  MODULES/FOLDERS/
       │  1:M                  │    CORE PROJECT TABLES  │                  DOCUMENT_FIELDS
       │   ┌─────────────────► │ lease_applications      │  ◄─────────────── (checklist masters)
       │   │                   │ mining_applications     │
       │   │                   │ environment_projects    │
       │   │                   │ ec_certificates         │
       │   │                   │ ppt_applications        │
       │   │                   │ dgps_surveys            │
       │   │                   │ drone_surveys           │
       │   │                   └────────────┬────────────┘
       │   │                                │ polymorphic (documentable/flowable/loggable)
       │   │      ┌─────────────────────────┼──────────────────────────┐
       │   │      ▼                         ▼                          ▼
       │   │  project_documents        project_flows            activity_logs
       │   │  (files + status)         (workflow steps)          (audit trail)
       │   │                                │
       │   └── mimas_credentials (1:1 lease)
       └── notifications (feed)
```

---

## 4. விரிவான அட்டவணை விவரங்கள் (Detailed Table Specifications)

### 4.1 பயனர்கள் & பாதுகாப்பு (Identity & Access)
* **`users`**: கணினி பயனர்கள் (அதிகாரிகள், ஆய்வாளர்கள், அட்மின்கள்).
  - `id, name, email, mobile_num, role_id (FK), branch_id (FK), image, password, status, timestamps`
* **`departments` (Branches)**: அலுவலக கிளைகள் மற்றும் தொடர்பு விவரங்கள்.
  - `id, branch_name, contact_person, mobile, address, city, state, pincode, status, timestamps`
* **`roles`**: பயனர் பதவிகள் (Super Admin, District Officer, Surveyor, etc.).
  - `id, name, description, timestamps`
* **`permissions`**: அனுமதி விசைகள் (எ.கா: `mining.create`, `lease.approve`).
  - `id, module, name, display_name, timestamps`
* **`role_permission`**: N:M பிவட் டேபிள்.
  - `id, role_id (FK), permission_id (FK)`

### 4.2 முதன்மை மாஸ்டர் அட்டவணைகள் (Master & Lookups)
* **`districts`**: தமிழ்நாடு மாவட்டங்களின் பட்டியல் (38 மாவட்டங்கள்).
* **`minerals`**: கனிமங்களின் பட்டியல் (Rough Stone, Granite, Limestone, Gravel, etc.).
* **`lease_categories`**: தமிழ்நாடு சிறு கனிம சலுகை விதிகள் (Rule 12, Rule 19(1), Rule 19-A, etc.).
* **`plan_types`**: சுரங்க திட்ட வகைகள் (Mining Plan, Modified Plan, Scheme of Mining).
* **`applicant_types`**: விண்ணப்பதாரர் நிலை (Individual, Partnership, Pvt Ltd).
* **`modules`**: கணினியின் முக்கிய தொகுதிகள் (lease, mining, environment, ppt, dgps, drone).
* **`folders`**: ஒவ்வொரு மாட்யூலிலும் வரும் கோப்புறைகள் (Field Log, Reports, Site Photos).
* **`document_fields`**: ஒவ்வொரு கோப்புறையிலும் சமர்ப்பிக்க வேண்டிய குறிப்பிட்ட ஆவணங்களின் சரிபார்ப்புப் பட்டியல் (Checklist).

### 4.3 வாடிக்கையாளர் & திட்ட அட்டவணைகள் (Customers & Core Projects)
* **`customers`**: சுரங்க குத்தகைதாரர் அல்லது நிறுவன விவரங்கள்.
  - `id, customer_name, company_name, mobile_num, email, district_id (FK), mineral_id (FK), gstin, pan, area, address, status`
* **`lease_applications`**: குத்தகை விண்ணப்ப படிவம் (7-படி வழிகாட்டி).
  - `id, customer_id (FK), application_no, district_id (FK), category_id (FK), contact_person, contact_mobile, survey_no, taluk, village, area_extent, mineral_id (FK), lease_period, status`
* **`mimas_credentials`**: MIMAS போர்ட்டல் உள்நுழைவு விவரங்கள் (1:1 with Lease).
  - `id, lease_application_id (FK, Unique), user_id (Encrypted), password (Encrypted), email, contact_number`
* **`mining_applications`**: சுரங்க திட்ட விண்ணப்பம் (Mining Portal).
  - `id, customer_id (FK), applicant_type_id (FK), district_id (FK), taluk, village, survey_number, mineral_id (FK), plan_type_id (FK), stage, report_no, status`
* **`environment_projects`**: சுற்றுச்சூழல் அனுமதி (B1 / B2).
  - `id, project_code, customer_id (FK), project_name, location, district_id (FK), category (B1/B2), contact_name, contact_phone, status`
* **`ec_certificates`**: சுற்றுச்சூழல் அனுமதி சான்றிதழ் விவரங்கள்.
  - `id, ec_ref_no, environment_project_id (FK), parivesh_app_no, applicant_name, approval_date, certificate_file, communication_type, status`
* **`ppt_applications`**: SEIAA/SEAC விளக்கக்காட்சி ஆவணங்கள்.
  - `id, application_no, customer_id (FK), project_name, district_id (FK), taluk_village, mineral_id (FK), status`
* **`dgps_surveys`**: டிஜிபிஎஸ் கள ஆய்வு விவரங்கள்.
  - `id, survey_no, customer_id (FK), lease_area, location, survey_date, survey_team, survey_status, report_status, gtm_report_file`
* **`drone_surveys`**: ட்ரோன் ஆய்வு மற்றும் வான்வழி வரைபடங்கள்.
  - `id, survey_no, customer_id (FK), lease_area, location, flight_date, drone_pilot, deliverable_file, gtms_report_file, survey_status`

### 4.4 பாலிமார்பிக் பகிர்வு அட்டவணைகள் (Shared Polymorphic Tables)
* **`project_documents`**: அனைத்து மாட்யூல்களின் கோப்புகளும் சேமிக்கப்படும் முதன்மை அட்டவணை.
  - `id, documentable_type, documentable_id, folder_id (FK), document_field_id (FK), file_name, file_path, file_type, file_size, status (pending/uploaded/validated/approved), review_note, reviewed_by (FK), uploaded_by (FK), timestamps, deleted_at`
* **`project_flows`**: படிநிலை சரிபார்ப்புகள் (Validation Stages 6.1 - 6.6).
  - `id, flowable_type, flowable_id, step_code, step_name, status, note, handled_by (FK), handled_at, timestamps`
* **`activity_logs`**: விரிவான கணினி செயல்பாட்டுத் தணிக்கை (Audit Trail).
  - `id, loggable_type, loggable_id, user_id (FK), action, description, properties (JSON), created_at`

---

## 5. எதிர்கால மேம்பாட்டிற்கான நிபுணர் ஆலோசனைகள் (Architectural Recommendations)

1. **Master Mining Site Entity (மேல்நிலை குத்தகை தளம்):**
   - தற்போது Lease, Mining, Environment, Surveys அனைத்தும் தனித்தனியாக உள்ளன. இவற்றுக்கு மேலே `mining_sites` அல்லது `quarry_leases` என்ற ஒரு முதன்மை அட்டவணையை உருவாக்கி, அதற்கு கீழ் அனைத்து விண்ணப்பங்களையும் கொண்டுவந்தால் ஒட்டுமொத்த குத்தகையின் முழுமையான வரலாற்றை ஒரே திரையில் பார்க்க முடியும்.
2. **Dynamic Checklist Generator:**
   - ஒரு புதிய திட்டத்தை உருவாக்கும் போது, அதன் மாட்யூலுக்குரிய `document_fields` அட்டவணையில் இருந்து ஆவண பதிவுகள் தானாகவே `status='pending'` உடன் உருவாக்கப்பட வேண்டும்.
3. **MIMAS Credential Vault:**
   - அரசு போர்ட்டல் நற்சான்றுகள் (Credentials) இருப்பதால், லாராவெலின் `Crypt::encryptString()` பயன்படுத்தி டேட்டாபேஸில் குறியாக்கம் (Encryption) செய்யப்பட வேண்டும்.
4. **Performance Composite Indexing:**
   - `(documentable_type, documentable_id, folder_id)` மற்றும் `(customer_id, status)` ஆகியவற்றிற்கு காம்போசிட் இண்டெக்ஸ் வைப்பது வினவல் வேகத்தை பல மடங்கு அதிகரிக்கும்.
