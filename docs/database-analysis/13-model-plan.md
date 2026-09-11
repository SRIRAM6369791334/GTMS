# 13 - Model Plan (Laravel Eloquent)

Planned Eloquent models, their traits, relationships, and any key behaviors.

**Legend:** 🔵 CONFIRMED FROM UI · 🟢 INFERRED · 🟡 NEEDS CONFIRMATION

---

## 13.1 Model Inventory

| Model | Table | Traits | Key Relationships |
| ----- | ----- | ------ | ----------------- |
| User | users | HasFactory, Notifiable | belongsTo Role, belongsTo Department; hasMany documents/uploads |
| Role | roles | HasFactory | belongsToMany Permission |
| Permission | permissions | HasFactory | belongsToMany Role |
| Department | departments | HasFactory | hasMany User |
| District | districts | HasFactory | hasMany Customer, hasMany Applications |
| Mineral | minerals | HasFactory | hasMany Customer, hasMany Applications |
| LeaseCategory | lease_categories | HasFactory | hasMany LeaseApplication |
| PlanType | plan_types | HasFactory | hasMany MiningApplication |
| ApplicantType | applicant_types | HasFactory | hasMany MiningApplication |
| Module | modules | HasFactory | hasMany Folder |
| Folder | folders | HasFactory | belongsTo Module; hasMany DocumentField, hasMany ProjectDocument |
| DocumentField | document_fields | HasFactory | belongsTo Folder; hasMany ProjectDocument |
| Customer | customers | HasFactory, SoftDeletes | belongsTo District, belongsTo Mineral; morphMany ProjectDocument? no — hasMany Projects |
| LeaseApplication | lease_applications | HasFactory, SoftDeletes | belongsTo Customer, District, LeaseCategory, Mineral; hasOne MimasCredential; morphMany ProjectDocument/ProjectFlow/ActivityLog |
| MimasCredential | mimas_credentials | HasFactory | belongsTo LeaseApplication |
| MiningApplication | mining_applications | HasFactory, SoftDeletes | belongsTo Customer, ApplicantType, District, Mineral, PlanType; morphMany docs/flows/logs |
| EnvironmentProject | environment_projects | HasFactory, SoftDeletes | belongsTo District; morphMany docs/flows/logs; hasMany EcCertificate |
| EcCertificate | ec_certificates | HasFactory, SoftDeletes | belongsTo EnvironmentProject; morphMany docs/logs |
| PptApplication | ppt_applications | HasFactory, SoftDeletes | belongsTo Customer, District, Mineral; morphMany docs/flows/logs |
| DgpsSurvey | dgps_surveys | HasFactory, SoftDeletes | belongsTo Customer; morphMany docs/logs |
| DroneSurvey | drone_surveys | HasFactory, SoftDeletes | belongsTo Customer; morphMany docs/logs |
| ProjectDocument | project_documents | HasFactory, SoftDeletes | morphTo documentable; belongsTo Folder, DocumentField, User (uploaded_by/reviewed_by) |
| ProjectFlow | project_flows | HasFactory, SoftDeletes | morphTo flowable |
| ActivityLog | activity_logs | HasFactory | morphTo loggable; belongsTo User |
| Notification | notifications | Notifiable | (framework) |
| Unit | units | HasFactory | (inventory) |
| ProductCategory | product_categories | HasFactory | hasMany Product |
| Product | products | HasFactory | belongsTo Department, ProductCategory |

---

## 13.2 Shared interface / trait

**Trait `Documentable`** (morphMany) applied to all project models:
```php
trait Documentable
{
    public function documents(): MorphMany
    {
        return $this->morphMany(ProjectDocument::class, 'documentable');
    }
    public function flows(): MorphMany
    {
        return $this->morphMany(ProjectFlow::class, 'flowable');
    }
    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}
```

---

## 13.3 Key behaviors
- **User:** cast `password` hashed; `show_password` is optional decrypted hint.
- **MimasCredential:** encrypt/decrypt `user_id` + `password` via `$casts`/accessors.
- **Statuses:** store as strings; provide constants/enums for `ProjectDocument::STATUSES` and `EnvironmentProject::STATUSES`.
- **Soft deletes** on all project + document models.
- **Role↔Permission** `belongsToMany` with `using(RolePermission::class)` for timestamps on pivot.

---

## 13.4 Ownership / authorship
- Most models add `created_by` (auth user id). Consider a `Blameable` trait OR rely on `activity_logs` — **RECOMMEND** activity_logs as the audit source of truth, keep the schema lean.

---

## 13.5 Notes
- EnvironmentPolicy (spatie/laravel-permission) NOT currently used — the project implements **custom** `roles` + `permissions` + `role_permission` pivot (`permissions[]` checkboxes). Models above mirror that custom structure.
- Exact model names are a recommendation; align with `routes/web.php` controller naming during implementation.
