# 09 - UI ↔ Database Mapping

Maps every UI screen/form to the table(s) and columns it reads/writes. This is the reverse index of the design.

**Legend:** 🔵 CONFIRMED · 🟢 INFERRED · 🟡 NEEDS CONFIRMATION

---

## 9.1 Authentication

| UI Screen | Table(s) | Read/Write | Notes |
| --------- | -------- | ---------- | ----- |
| Login | users | R | email + password check; status active |
| Register (future) | users | W | |
| Forgot Password | password_resets | R/W | framework |

## 9.2 Department (Branch)

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index (list) | departments | all + count | R |
| create modal | departments | branch_name, contact_person, mobile, address, city, state, pincode | W |
| edit modal | departments | same + status | W |
| delete | departments | status=0 / delete | W |

## 9.3 Role

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index (list) | roles | name + permissions count (derived) | R |
| create modal | roles, role_permission | name + permissions[] | W |
| edit modal | roles, role_permission | name + permissions[] | W |

## 9.4 User

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index (list) | users, roles, departments | name, role, branch, mobile, email, status | R |
| create modal | users | name, role_id, branch_id, mobile_num, image, email, password, show_password | W |
| edit modal | users | same minus password | W |

## 9.5 Customer Directory

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| directory list | customers | name, company, mineral, district, mobile, status | R |
| add modal | customers | customer_name, company_name, mobile, email, district, mineral, gstin, pan, area, status, address | W |
| edit modal | customers | same | W |
| view profile | customers | all + derived counts | R |

## 9.6 Lease Application

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| list | lease_applications | application_no, client, district, category, docs count, status | R |
| step 1 | lease_applications | client_name→customer_id, district_id | W |
| step 2 | lease_applications | contact_person, contact_mobile | W |
| step 3 | lease_applications | category_id | W |
| step 4 | (folders display) | from folders master | R |
| step 5 | project_documents (+document_fields) | checklist + file uploads | W |
| step 6 | mimas_credentials | user_id, password, email, contact_number | W |
| step 7 (preview) | lease_applications + related | survey_no, village, area, mineral, lease_period | R |
| viewapplication | lease_applications + documents + folders + project_flows + activity_logs | full detail | R |

## 9.7 Mining Application

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index (list) | mining_applications | client, district, mineral, plan type, stage | R |
| new step 1 | customers OR mining_applications(client snapshot) | client fields, applicant_type | W |
| new step 2 | mining_applications | district_id, taluk, village, survey_number | W |
| new step 3 | mining_applications | mineral_id | W |
| new step 4 | mining_applications | plan_type_id | W |
| new step 5 | (folders) | folders master | R |
| new step 6 (preview) | mining_applications | summary | R |
| projectfolder | mining_applications + folders + project_documents | folder progress | R |
| document | project_documents | uploads/status per folder | R/W |
| process | project_flows + activity_logs + project_documents | validation stages, audit | R/W |

## 9.8 Environment B1

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index | environment_projects + folders | folder progress, activity | R |
| subcat1 | project_documents | Site & Mining checklist | R/W |
| subcat2 | project_documents | EIA & TNPCB checklist | R/W |

## 9.9 Environment B2 (working backend)

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index | environment_projects | app no, client, project, category, docs count, status, updated | R |
| wizard steps 1–3 | environment_projects | client, project, location, district, category, contact | W |
| wizard step 4 | project_documents | 29-item checklist + uploads | W |
| wizard step 5 | project_flows | validation | W |
| wizard step 6 | project_flows / status | approve/report/archive | W |
| show | environment_projects + project_documents + project_flows + activity_logs | full detail | R |
| upload (modal) | project_documents | file + status=uploaded | W |
| review (modal) | project_documents | status + review_note + reviewed_by | W |
| status change | environment_projects | status | W |
| activity | activity_logs | log entries | R/W |

## 9.10 EC Certificate

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index | ec_certificates | ref no, applicant, project, parivesh status, cert status, comm status | R |
| wizard step 1 | ec_certificates | ec_ref_no, parivesh_app_no, applicant_name, approval_date | W |
| step 2–3 | document storage | certificate file | R/W |
| step 4 | project_documents | EC Certificate + Final Project Docs folders | W |
| step 5 | ec_certificates | communication_type, recipient_email, communication_note | W |
| step 6 (preview) | ec_certificates | summary | R |

## 9.11 PPT Department

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index | ppt_applications | app no, client, district, mineral, folders, status, updated | R |
| wizard 1–3 | ppt_applications | client, project, mobile, email, district, taluk/village, mineral | W |
| wizard 4 | (folders) | 11 folders master | R |
| wizard 5 | project_documents | uploads | W |
| wizard 6 | project_flows / status | approve/report/archive | W |
| wizard 7 | ppt_applications | preview | R |

## 9.12 DGPS Survey

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index | dgps_surveys | survey no, client, location, area, survey status, report status | R |
| wizard 1 | dgps_surveys | survey_no, customer, lease_area, location | W |
| wizard 2 | dgps_surveys | survey_date, survey_team | W |
| wizard 3 | project_documents | data processing uploads | W |
| wizard 4 | dgps_surveys | report | W |
| wizard 5 | dgps_surveys | gtm_report_file | W |

## 9.13 Drone Survey

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| index | drone_surveys | survey no, client, location, area, survey status, deliverables | R |
| wizard 1 | drone_surveys | survey_no, customer, lease_area, location | W |
| wizard 2 | drone_surveys | flight_date, drone_pilot | W |
| wizard 3 | project_documents | processing uploads | W |
| wizard 4 | drone_surveys | deliverable_file | W |
| wizard 5 | drone_surveys | gtms_report_file | W |

## 9.14 Master (inventory)

| UI Screen | Table(s) | Columns | Read/Write |
| --------- | -------- | ------- | ---------- |
| unit index/create | units | units | R/W |
| category index/create | product_categories | cat_code, cat_name | R/W |
| product index/create | products | branch_id, cat_id, pro_name, gst, cast_per, mrp, qty, discounts | R/W |
| productstock | products (derived) | total/available/sales stock | R |
