# 12 - Database Migration Plan

Ordered plan for Laravel migrations. Order matters because foreign keys reference earlier tables.

---

## 12.1 Migration Order (topological)

| # | Migration | Creates | Depends On |
| - | --------- | ------- | ---------- |
| 1 | 0001-01-01 | users, password_resets (framework/fortify) | — |
| 2 | create_permissions_table | permissions | — |
| 3 | create_roles_table | roles | — |
| 4 | create_role_permission_table | role_permission (pivot) | roles, permissions |
| 5 | create_departments_table | departments | — |
| 6 | alter_users_add_role_branch | users.role_id, users.branch_id | roles, departments |
| 7 | create_personal_access_tokens | personal_access_tokens | users |
| 8 | create_districts_table | districts | — |
| 9 | create_minerals_table | minerals | — |
| 10 | create_lease_categories_table | lease_categories | — |
| 11 | create_plan_types_table | plan_types | — |
| 12 | create_applicant_types_table | applicant_types | — |
| 13 | create_modules_table | modules | — |
| 14 | create_folders_table | folders | modules |
| 15 | create_document_fields_table | document_fields | folders |
| 16 | create_customers_table | customers | districts, minerals |
| 17 | create_lease_applications_table | lease_applications | customers, districts, lease_categories, minerals |
| 18 | create_mimas_credentials_table | mimas_credentials | lease_applications |
| 19 | create_mining_applications_table | mining_applications | customers, applicant_types, districts, minerals, plan_types |
| 20 | create_environment_projects_table | environment_projects | districts |
| 21 | create_ec_certificates_table | ec_certificates | environment_projects |
| 22 | create_ppt_applications_table | ppt_applications | customers, districts, minerals |
| 23 | create_dgps_surveys_table | dgps_surveys | customers |
| 24 | create_drone_surveys_table | drone_surveys | customers |
| 25 | create_project_documents_table | project_documents (polymorphic) | folders, document_fields |
| 26 | create_project_flows_table | project_flows (polymorphic) | — |
| 27 | create_activity_logs_table | activity_logs (polymorphic) | users |
| 28 | create_notifications_table | notifications | — |
| 29 | create_units_table | units | — |
| 30 | create_product_categories_table | product_categories | — |
| 31 | create_products_table | products | departments, product_categories |

---

## 12.2 Naming Conventions
- `create_<plural>_table`
- `add_<column>_to_<table>_table` for alterations (e.g., users role/branch)
- `alter_<table>_table_<change>` for modifications

---

## 12.3 Key Migration Snippets (patterns)

### Polymorphic project_documents
```php
Schema::create('project_documents', function (Blueprint $table) {
    $table->id();
    $table->morphs('documentable');
    $table->foreignId('folder_id')->nullable()->constrained('folders')->nullOnDelete();
    $table->foreignId('document_field_id')->nullable()->constrained('document_fields')->nullOnDelete();
    $table->string('file_name')->nullable();
    $table->string('file_path')->nullable();
    $table->string('file_type', 10)->nullable();
    $table->unsignedBigInteger('file_size')->nullable();
    $table->string('status', 30)->default('pending');
    $table->text('review_note')->nullable();
    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('reviewed_at')->nullable();
    $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('uploaded_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['documentable_type', 'documentable_id']);
    $table->index(['documentable_type', 'documentable_id', 'folder_id']);
});
```

### users alter
```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
    $table->foreignId('branch_id')->nullable()->after('role_id')->constrained('departments')->nullOnDelete();
    $table->string('mobile_num', 15)->nullable()->after('branch_id');
    $table->string('image')->nullable();
    $table->string('show_password')->nullable();
    $table->tinyInteger('status')->default(1);
});
```

---

## 12.4 Seeders
| Seeder | Data |
| ------ | ---- |
| RolesSeeder | super_admin, admin, officer, viewer (🟡) |
| PermissionsSeeder | all permission keys (from sidebar gates) |
| DistrictsSeeder | 38 TN districts |
| MineralsSeeder | 6 mineral types |
| LeaseCategoriesSeeder | 8 rule categories |
| PlanTypesSeeder | 4 plan types |
| ApplicantTypesSeeder | 4 applicant types |
| ModulesSeeder | 7 modules |
| FoldersSeeder | module-keyed folders |
| DocumentFieldsSeeder | per-folder checklist (needs full UI scrape) |
| UnitsSeeder | Nos |
| AdminUserSeeder | default super admin |

---

## 12.5 Rollback Strategy
- Rollback in exact reverse order (31 → 1).
- Drop FK-referencing tables before their referenced tables.
- Consider `Schema::disableForeignKeyConstraints()` in unit-test seeds for pivot/marker tables only.

---

## 12.6 Notes
- **Do NOT create these migrations yet** — architecture must be approved first (per project constraints).
- All status columns use lowercase snapshots matching env B2 backend (`draft/validation/approved/reported/archived`, `pending/uploaded/validated/approved/revision_required`).
