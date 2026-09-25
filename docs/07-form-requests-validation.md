# 07 — Form Requests & Input Validation Catalog

**Document Version:** 1.0.0  
**Target Codebase:** `c:\xampp\htdocs\GTMS\gtms`  
**Classification:** Complete Validation & Sanitization Catalog  
**Author:** Spec Miner Survey 2 (Backend Logic Specialist)  

---

## 1. Validation Architecture & Design Rationale

In GTMS, validation is implemented via **Controller-Inline Validation** using `$request->validate([...])` and `Illuminate\Support\Facades\Validator::make(...)`. 

### 1.1 Architectural Characteristics
- **Zero Dedicated FormRequest Classes:** There are 0 files under `app/Http/Requests/`. All rules, custom messages, pre-validation transformations, and error formatting reside within controller action methods.
- **Pre-Validation Normalization:** Before passing data to the validator, controllers sanitize inputs (e.g. stripping non-digits from Aadhaar numbers and re-formatting them into standard 4-4-4 blocks).
- **Soft-Delete Unique Collision Protection:** Unique database constraints in MySQL operate globally across active and soft-deleted rows. GTMS validation explicitly scopes unique checks to non-deleted records (`Rule::unique(...)->whereNull('deleted_at')`), pairing this with automatic restoration logic if an existing soft-deleted profile is submitted.
- **Dual Response Formatting:**
  - **AJAX Endpoints:** Return HTTP 422 JSON payloads with structured error objects:
    ```json
    {
      "status": 0,
      "errors": {
        "mimas_no": ["This Customer Unique ID is already registered by an active customer."]
      }
    }
    ```
  - **Web Form Endpoints:** Redirect back to the previous wizard step with flash error bags (`back()->withErrors([...])->withInput()`).

---

## 2. Master Validation & Regex Catalog

Below is the exhaustive catalog of validation rules applied across each controller endpoint.

### 2.1 Customer & Identity Validation (`CustomerDirectoryController.php`)

#### Method: `store(Request $request)` & `update(Request $request)`
```php
[
    'mimas_no'                 => ['required', 'string', 'max:50', Rule::unique('customers', 'mimas_no')->whereNull('deleted_at')],
    'mimas_number'             => 'nullable|string|max:100',
    'mimas_status'             => 'nullable|string|max:100',
    'customer_name'            => 'required|string|max:255',
    'secondary_contact_person' => 'nullable|string|max:255',
    'company_name'             => 'nullable|string|max:255',
    'mobile_num'               => 'required|string|max:15',
    'secondary_mobile_num'     => 'nullable|string|max:15|different:mobile_num',
    'email'                    => 'nullable|email|max:255',
    'district_id'              => 'required|exists:districts,id',
    'mineral_id'               => 'nullable|exists:minerals,id',
    'area'                     => 'nullable|numeric|min:0',
    'pan'                      => 'nullable|string|max:10',
    'aadhaar_no'               => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{4}[ -]?[0-9]{4}[ -]?[0-9]{4}$/', Rule::unique('customers', 'aadhaar_no')->whereNull('deleted_at')],
    'gstin'                    => 'nullable|string|max:15',
    'address'                  => 'nullable|string',
    'status'                   => 'required|in:0,1',
]
```

#### Custom Validation Error Messages:
```php
[
    'aadhaar_no.regex'  => 'The Aadhaar number must be a valid 12-digit number (e.g. 9876-5432-1012 or 987654321012).',
    'mimas_no.unique'   => 'This Customer Unique ID is already registered by an active customer.',
    'aadhaar_no.unique' => 'This Aadhaar number is already registered by an active customer.',
]
```

#### Pre-Validation Sanitization Pipeline:
1. **Aadhaar Auto-Grouping:**
   ```php
   if ($request->filled('aadhaar_no')) {
       $digits = preg_replace('/\D/', '', $request->input('aadhaar_no'));
       if (strlen($digits) === 12) {
           $request->merge([
               'aadhaar_no' => substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8, 4)
           ]);
       }
   }
   ```
2. **Casting Empty Strings to Null:** Converts optional fields (`company_name`, `pan`, `aadhaar_no`, `gstin`) from empty strings `""` to `null` to prevent MySQL unique index collisions on blank values.
3. **Upper-casing Tax IDs:** Applies `strtoupper(trim($data['pan']))` and `strtoupper(trim($data['gstin']))`.

---

### 2.2 Lease Application Intake Validation (`CustomerController.php`)

#### Step 1: Applicant & Mineral Concession Details (`saveStep1`)
```php
[
    'customer_id'              => 'nullable|integer',
    'mimas_no'                 => 'required|string|max:50',
    'client_name'              => 'required|string|max:255',
    'secondary_contact_person' => 'nullable|string|max:255',
    'company_name'             => 'required|string|max:255',
    'district_id'              => 'required|integer|exists:districts,id',
    'mineral_ids'              => 'nullable|array',
    'mineral_ids.*'            => 'integer|exists:minerals,id',
    'mineral_id'               => 'nullable|integer|exists:minerals,id',
    'other_mineral_name'       => 'nullable|string|max:255',
    'mobile_num'               => 'required|string|max:15',
    'secondary_mobile_num'     => 'nullable|string|max:15|different:mobile_num',
    'email'                    => 'nullable|email|max:255',
    'pan'                      => 'required|string|max:10',
    'aadhaar_no'               => 'required|string|max:14',
    'gstin'                    => 'nullable|string|max:15',
    'area'                     => 'nullable|string|max:20',
    'address'                  => 'nullable|string|max:500',
]
```

#### Step 2: Contact & Portal Credentials (`saveStep2`)
```php
[
    'contact_person'           => 'required|string|max:255',
    'contact_mobile'           => 'required|string|max:15',
    'secondary_contact_person' => 'nullable|string|max:255',
    'secondary_contact_mobile' => 'nullable|string|max:15|different:contact_mobile',
    'mimas_user_id'            => 'required|string|max:100',
    'mimas_password'           => 'nullable|string|max:255',
    'mimas_email'              => 'required|email|max:255',
    'mimas_contact'            => 'nullable|string|max:15',
]
```
*Special Rule (Password Sanitization):* If `mimas_password` equals `'__UNCHANGED__'` or is submitted empty, the controller preserves the previously encrypted password from the database or session draft, protecting credentials from cleartext DOM exposure.

#### Step 3: Statutory Category (`saveStep3`)
```php
[
    'category_id' => 'required|integer|exists:lease_categories,id',
]
```

#### Step 5: Document Upload (`uploadDocument`)
```php
[
    'file'         => 'required|file|max:25600|mimes:pdf,png,jpg,jpeg,kml,xml,txt,doc,docx,dwg',
    'doc_item'     => 'nullable',
    'folder_id'    => 'required|integer',
    'doc_name'     => 'nullable|string|max:255',
    'is_mandatory' => 'nullable',
]
```

#### Step 6: Handling Team Allocation (`saveStep6`)
```php
[
    'handlers'         => 'nullable|array',
    'handlers.*.name'  => 'nullable|string|max:255',
    'handlers.*.role'  => 'nullable|string|max:255',
    'handlers.*.notes' => 'nullable|string|max:1000',
]
```

#### Step 7: Financial Settlement (`saveStep7`)
```php
[
    'product_value'  => 'required|numeric|min:0',
    'paid_amount'    => 'required|numeric|min:0',
    'payment_status' => 'nullable|string|in:paid,partial,pending',
]
```

#### Process Scrutiny: Scrutiny Validation Loop (`validateApplication` & `rejectApplication`)
```php
// Validation pass/fail:
[
    'action'  => 'required|in:pass,fail',
    'remarks' => 'nullable|string|max:1000',
]

// Rejection for revision:
[
    'reason' => 'required|string|max:1000',
]

// Document granular status:
[
    'status' => 'required|in:pending,uploaded,validated,approved,revision_required',
    'note'   => 'nullable|string|max:500',
]
```

---

### 2.3 Mining Plan Validation (`MiningController.php`)

#### Application Registration (`store`)
```php
[
    'nature_of_work_id'   => 'required|exists:nature_of_works,id',
    'applicant_type_id'   => 'required|exists:applicant_types,id',
    'district_id'         => 'required|exists:districts,id',
    'taluk'               => 'nullable|string|max:255',
    'village'             => 'nullable|string|max:255',
    'survey_numbers_text' => 'nullable|string|max:500',
    'area_extent_ha'      => 'nullable|numeric',
    'other_mineral_name'  => 'nullable|string|max:255',
]
```
- **Conditional Rule for Mining Plan:**
  If `nature_of_work_id` corresponds to 'Mining Plan', the validator enforces:
  ```php
  'plan_type_id'    => 'required|exists:plan_types,id',
  'mineral_ids'     => 'required|array|min:1',
  'mineral_ids.*'   => 'exists:minerals,id',
  ```
- **Conditional Rule for New Customer Profile:**
  If `customer_id` is omitted:
  ```php
  'client_name'              => 'required|string|max:255',
  'secondary_contact_person' => 'nullable|string|max:255',
  'company_name'             => 'required|string|max:255',
  'mobile_num'               => 'required|string|max:20',
  'secondary_mobile_num'     => 'nullable|string|max:20',
  'pan'                      => 'nullable|string|max:10',
  'aadhaar_no'               => 'nullable|string|max:14',
  ```

#### Document Upload (`uploadDocument`)
```php
[
    'mining_application_id' => 'required|exists:mining_applications,id',
    'document_id'           => 'required|exists:mining_documents,id',
    'file'                  => 'required|file|max:25600|mimes:pdf,doc,docx,jpg,jpeg,png,kml,kmz,zip,dwg,dxf',
]
```

---

### 2.4 Environment Clearance B1 & B2 Validation (`EnverionsoneController.php` & `EnvironmentalB2Controller.php`)

#### Project Registration (`EnverionsoneController@store`)
```php
[
    'category'      => 'required|in:B1,B2',
    'sub_category'  => 'nullable|in:SC1,SC2', // Required if category == 'B1'
    'customer_id'   => 'nullable|integer|exists:customers,id',
    'client_name'   => 'required|string|max:255',
    'company_name'  => 'nullable|string|max:255',
    'project_name'  => 'required|string|max:255',
    'district_id'   => 'required|exists:districts,id',
    'location'      => 'nullable|string|max:255',
    'contact_name'  => 'nullable|string|max:255',
    'contact_phone' => 'required|string|max:20',
    'contact_email' => 'nullable|email|max:255',
    'mimas_no'      => 'nullable|string|max:50',
]
```

#### Document Upload & Review:
```php
// Upload:
['file' => 'required|file|max:25600']

// Custom Document Add:
[
    'folder_id'     => 'required|integer',
    'document_name' => 'required|string|max:255',
    'file'          => 'required|file|max:25600',
]

// Document Review:
[
    'status'      => 'required|in:approved,revision_required',
    'review_note' => 'nullable|string|max:500',
]

// Project Status Lifecycle:
['status' => 'required|in:draft,validation,approved,reported,archived']
```

---

### 2.5 EC Certificate Issuance Validation (`EcCertificateController.php`)

#### Step 1: Statutory Order Parameters (`saveStep`)
```php
[
    'environment_project_id' => 'required|exists:environment_projects,id',
    'ec_ref_no'              => 'required|string|max:100',
    'parivesh_app_no'        => 'nullable|string|max:100',
    'applicant_name'         => 'required|string|max:255',
    'issue_date'             => 'required|date',
    'validity_years'         => 'nullable|integer|min:1|max:30', // Clamped to 5 by default
    'communication_type'     => 'required|in:Grant,Rejection,ToR',
    'conditions_summary'     => 'nullable|string|max:2000',
]
```

#### Step 2: Certificate PDF Upload:
```php
['certificate_file' => 'nullable|file|mimes:pdf,docx,jpg,png|max:25600']
```

#### Step 5: Statutory Dispatch Configuration:
```php
[
    'communication_type' => 'required|in:Grant,Rejection,ToR',
    'recipient_email'    => 'required|email|max:255',
    'recipient_phone'    => 'nullable|string|max:20',
    'communication_note' => 'nullable|string|max:1000',
    'send_sms_alert'     => 'nullable|boolean',
]
```

---

### 2.6 Surveys & Compliance Validation

#### DGPS Survey Document Upload (`DgpsSurveyController@uploadDocument`):
```php
[
    'file'          => 'required|file|max:51200', // 50MB for raw GNSS RINEX observation data
    'survey_id'     => 'nullable|integer',
    'document_name' => 'required|string|max:255',
]
```

#### EC Half-Yearly Compliance Upload (`EcComplianceController@uploadDocument`):
```php
[
    'file'            => 'required|file|max:51200', // 50MB for NABL laboratory sample test data
    'compliance_id'   => 'nullable|integer',
    'folder_category' => 'required|string',
    'document_name'   => 'required|string|max:255',
]
```

#### PPT Presentation Upload (`PptDepartmentController@uploadDocument`):
```php
[
    'file'           => 'required|file|max:25600',
    'application_id' => 'nullable|integer',
    'folder_id'      => 'nullable|integer',
    'document_name'  => 'required|string|max:255',
]
```

---

### 2.7 User & Access Control Validation (`UserController.php` & `RolesController.php`)

#### User Store & Update:
```php
// Store:
[
    'name'      => 'required|string|max:255',
    'email'     => 'required|email|unique:users,email',
    'password'  => 'required|min:6',
    'role_id'   => 'required|exists:roles,id',
    'branch_id' => 'required|exists:branches,id',
    'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
]

// Update:
[
    'id'        => 'required|exists:users,id',
    'name'      => 'required|string|max:255',
    'email'     => 'required|email|unique:users,email,' . $request->id,
    'password'  => 'nullable|min:6',
    'role_id'   => 'required|exists:roles,id',
    'branch_id' => 'required|exists:branches,id',
    'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
]
```

#### Role Store & Update:
```php
// Store:
[
    'name'        => 'required|unique:roles,name',
    'permissions' => 'nullable|array',
]

// Update:
[
    'id'          => 'required|exists:roles,id',
    'name'        => 'required|unique:roles,name,' . $request->id,
    'permissions' => 'nullable|array',
]
```

---

## 3. Summary Matrix of File & Data Limits

| Data Type | Validation Rule | Max Constraint | Notes |
|:---|:---|:---|:---|
| **Aadhaar Number** | `/^[0-9]{4}[ -]?[0-9]{4}[ -]?[0-9]{4}$/` | 14 chars (`XXXX-XXXX-XXXX`) | Normalized before validation |
| **PAN Number** | `string\|max:10` | 10 chars | Forced uppercase |
| **GSTIN** | `string\|max:15` | 15 chars | Forced uppercase |
| **Mobile Number** | `string\|max:15` or `max:20` | 15 / 20 chars | Strips `+91` during normalized lookups |
| **Standard Documents** | `file\|max:25600` | 25 Megabytes | PDF, PNG, JPG, CAD (DWG), KML |
| **Survey / Lab Data** | `file\|max:51200` | 50 Megabytes | Raw GNSS RINEX, NABL testing sheets |
| **Avatar Images** | `image\|max:2048` | 2 Megabytes | JPEG, PNG, WEBP |
| **Certificate Validity** | `integer\|min:1\|max:30` | 30 Years | Defaults to 5 years |

---
*End of Document 07 — Form Requests & Input Validation Catalog*
