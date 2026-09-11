# 14 - Validation Plan

Form/API validation rules for each write operation, aligned to the HTML input names where available.

**Legend:** 🔵 CONFIRMED FROM UI / CONTROLLER · 🟢 INFERRED · 🟡 NEEDS CONFIRMATION

---

## 14.1 Auth & Users

### Login
| Field | Rules |
| ----- | ----- |
| email | required, string, email-or-login-id (could be user code) 🟡 |
| password | required, string |

### User create
| Field | Rules |
| ----- | ----- |
| name | required, string, max:255 |
| role_id | required, exists:roles,id |
| branch_id | nullable, exists:departments,id (or departments) |
| mobile_num | nullable, string, max:15 |
| image | nullable, image, mimes:jpg,jpeg,png, max:2048 |
| email | required, string, email, max:255, unique:users,email |
| password | required, string, min:8, confirmed (on create) |

> 🔵 UserController stores `email`, `password` (bcrypt), and validates email unique.

### User edit
Same minus required password; password optional on edit (min:8, nullable).

## 14.2 Department / Branch
| Field | Rules |
| ----- | ----- |
| branch_name | required, string, max:255, unique:departments,branch_name (ignore self) |
| contact_person | required, string, max:255 |
| mobile | required, string, max:15 |
| address | required, string |
| city / state / pincode | nullable, string |
| status | nullable, boolean |

## 14.3 Role
| Field | Rules |
| ----- | ----- |
| name | required, string, max:255, unique:roles,name (ignore self) |
| permissions[] | nullable, array, exists:permissions,id |

## 14.4 Customer
| Field | Rules |
| ----- | ----- |
| customer_name | required, string, max:255 |
| company_name | required, string, max:255 |
| mobile_num | required, string, max:15 |
| email | required, string, email, max:255 🟡 (nullable per edit) |
| district | required, exists:districts,id (or string if free text) 🟡 |
| mineral | required, exists:minerals,id (or string) 🟡 |
| gstin | nullable, string, max:15 |
| pan | required, string, size:10, regex uppercase alpha-numeric 🟢 |
| area | nullable, numeric, min:0 |
| status | required, in:[0,1] |
| address | nullable, string |

## 14.5 Lease Application + MIMAS
### Step 1–3
| Field | Rules |
| ----- | ----- |
| client / customer_id | required, exists:customers,id |
| district_id | required, exists:districts,id |
| category_id | required, exists:lease_categories,id |
| contact_person | required, string |
| contact_mobile | required, string, max:15 |

### MIMAS (step 6) 🔵
| Field | Rules |
| ----- | ----- |
| user_id | required, string |
| password | required, string (will be encrypted) |
| email | required, email |
| contact_number | required, string, max:15 |

## 14.6 Mining Application
| Field | Rules |
| ----- | ----- |
| client / customer_id | required |
| applicant_type_id | required, exists:applicant_types,id |
| mobile | required, string, max:15 |
| email | required, email |
| district_id | required, exists:districts,id |
| taluk / village / survey_number | nullable, string |
| mineral_id | required, exists:minerals,id |
| plan_type_id | required, exists:plan_types,id |

## 14.7 Environment B2 (🔵 working backend)
### Store project
| Field | Rules |
| ----- | ----- |
| client_name | required, string, max:255 |
| project_name | required, string, max:255 |
| location | nullable, string, max:255 |
| district | nullable, string |
| contact_name / contact_phone / contact_email | nullable |

### Document upload
| Field | Rules |
| ----- | ----- |
| file | required, file, mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,zip, max:10240 (10MB) |

### Document review
| Field | Rules |
| ----- | ----- |
| status | required, in:validated,approved,revision_required |
| review_note | nullable, string |

### Project status
| Field | Rules |
| ----- | ----- |
| status | required, in:draft,validation,approved,reported,archived |

## 14.8 EC Certificate
| Field | Rules |
| ----- | ----- |
| ec_ref_no | required, string, unique:ec_certificates,ec_ref_no |
| parivesh_app_no | required, string |
| applicant_name | required, string, max:255 |
| approval_date | nullable, date |
| certificate_file | nullable, file, mimes:pdf, max:10240 |
| communication_type | nullable, in:grant,rejection |
| recipient_email | nullable, email |
| communication_note | nullable, string |

## 14.9 PPT / DGPS / Drone
Similar structural rules (client, district, mineral required; date/team/pilot nullable; uploads file with mime allowlist).

## 14.10 Master
### Unit
`units` → required, string, max:100, unique:units,units
### Category
`cat_code` → required, string, max:50, unique; `cat_name` → required, string, max:255, unique
### Product (🔵 backend)
`branch_id`, `cat_id` → required, exists; `pro_name` → required, string; `gst`, `cast_per`, `mrp`, `qty` → required, numeric ≥ 0; `discount_1/2/3` → nullable, numeric

---

## 14.11 Global validation notes
- All file uploads: allowlist + size cap (10MB) matching UI dropzone.
- All selects reference master tables (exists rules) — implies master rows must be seeded before data entry.
- Date fields: `date` format.
- Use **Laravel Form Requests** per module (`app/Http/Requests/`) for reusable validation + authorization.
- Sensitive fields (MIMAS) encrypted before persist — never log plaintext.
