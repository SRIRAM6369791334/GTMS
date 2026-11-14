# 🗄️ GTMS Minimal Database Schema & Model Reference
> **Token-Saver Reference:** Use this compact dictionary to look up exact table names, column types, foreign keys, and relations without reading migrations or running SQL queries.

### Table: `activity_logs`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `loggable_type` | `varchar(100)` | `YES` | `MUL` | `NULL` |  |
| `loggable_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `user_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `action` | `varchar(100)` | `NO` | `` | `NULL` |  |
| `description` | `text` | `YES` | `` | `NULL` |  |
| `ip_address` | `varchar(45)` | `YES` | `` | `NULL` |  |
| `user_agent` | `text` | `YES` | `` | `NULL` |  |
| `old_values` | `longtext` | `YES` | `` | `NULL` |  |
| `new_values` | `longtext` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `NO` | `MUL` | `current_timestamp()` |  |

### Table: `applicant_types`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `name` | `varchar(100)` | `NO` | `UNI` | `NULL` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `application_handlers`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `application_type` | `varchar(50)` | `YES` | `MUL` | `NULL` |  |
| `application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `handlerable_type` | `varchar(255)` | `YES` | `MUL` | `NULL` |  |
| `handlerable_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `role` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `notes` | `text` | `YES` | `` | `NULL` |  |
| `sort_order` | `int(11)` | `NO` | `` | `0` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `application_payments`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `application_type` | `varchar(50)` | `YES` | `MUL` | `NULL` |  |
| `application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `payable_type` | `varchar(255)` | `YES` | `MUL` | `NULL` |  |
| `payable_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `product_value` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `notes` | `text` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `archived_activity_logs`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `loggable_type` | `varchar(100)` | `YES` | `MUL` | `NULL` |  |
| `loggable_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `user_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `action` | `varchar(100)` | `NO` | `` | `NULL` |  |
| `description` | `text` | `YES` | `` | `NULL` |  |
| `ip_address` | `varchar(45)` | `YES` | `` | `NULL` |  |
| `user_agent` | `text` | `YES` | `` | `NULL` |  |
| `old_values` | `longtext` | `YES` | `` | `NULL` |  |
| `new_values` | `longtext` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `MUL` | `NULL` |  |
| `archived_at` | `timestamp` | `NO` | `` | `current_timestamp()` |  |

### Table: `bank_accounts`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `account_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `account_number` | `varchar(255)` | `NO` | `UNI` | `NULL` |  |
| `bank_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `branch_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `ifsc_code` | `varchar(20)` | `NO` | `` | `NULL` |  |
| `is_default` | `tinyint(1)` | `NO` | `` | `0` |  |
| `is_active` | `tinyint(1)` | `NO` | `` | `1` |  |
| `created_by` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `branches`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `branch_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `contact_person` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `mobile` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `address` | `text` | `YES` | `` | `NULL` |  |
| `city` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `state` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `country` | `varchar(255)` | `NO` | `` | `India` |  |
| `pincode` | `varchar(10)` | `YES` | `` | `NULL` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `categories`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `cat_code` | `varchar(255)` | `NO` | `UNI` | `NULL` |  |
| `cat_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `delete_status` | `tinyint(4)` | `NO` | `` | `0` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `chunked_upload_parts`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `chunked_upload_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `chunk_index` | `int(11)` | `NO` | `` | `NULL` |  |
| `chunk_size` | `int(11)` | `NO` | `` | `NULL` |  |
| `chunk_path` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `chunk_hash` | `varchar(64)` | `YES` | `` | `NULL` |  |
| `is_uploaded` | `tinyint(1)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `chunked_uploads`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `upload_token` | `varchar(64)` | `NO` | `UNI` | `NULL` |  |
| `user_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `file_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `original_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `mime_type` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `total_size` | `bigint(20)` | `NO` | `` | `NULL` |  |
| `chunk_size` | `int(11)` | `NO` | `` | `NULL` |  |
| `total_chunks` | `int(11)` | `NO` | `` | `NULL` |  |
| `uploaded_chunks` | `int(11)` | `NO` | `` | `0` |  |
| `target_module` | `varchar(255)` | `YES` | `MUL` | `NULL` |  |
| `reference_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_hash` | `varchar(64)` | `YES` | `` | `NULL` |  |
| `checksum_sha256` | `varchar(64)` | `YES` | `` | `NULL` |  |
| `status` | `enum('pending','uploading','assembled','processing','completed','failed','cancelled')` | `NO` | `MUL` | `pending` |  |
| `error_message` | `text` | `YES` | `` | `NULL` |  |
| `metadata` | `longtext` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `customers`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `customer_name` | `varchar(255)` | `NO` | `MUL` | `NULL` |  |
| `secondary_contact_person` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `company_name` | `varchar(255)` | `YES` | `MUL` | `NULL` |  |
| `mimas_no` | `varchar(50)` | `YES` | `UNI` | `NULL` |  |
| `mimas_number` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `mimas_status` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `slug` | `varchar(255)` | `YES` | `UNI` | `NULL` |  |
| `mobile_num` | `varchar(15)` | `NO` | `MUL` | `NULL` |  |
| `secondary_mobile_num` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `email` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `parivesh_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `email_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `district_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `mineral_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `pan` | `varchar(10)` | `YES` | `MUL` | `NULL` |  |
| `aadhaar_no` | `varchar(20)` | `YES` | `UNI` | `NULL` |  |
| `gstin` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `area` | `varchar(50)` | `YES` | `` | `NULL` |  |
| `address` | `text` | `YES` | `` | `NULL` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `user_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `dgps_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `dgps_survey_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `status` | `varchar(30)` | `NO` | `` | `pending` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `dgps_points`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `dgps_survey_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `pillar_no` | `varchar(50)` | `NO` | `` | `NULL` |  |
| `latitude` | `decimal(11,8)` | `NO` | `` | `NULL` |  |
| `longitude` | `decimal(11,8)` | `NO` | `` | `NULL` |  |
| `elevation` | `decimal(8,2)` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `dgps_surveys`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `survey_no` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `field_book_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `lease_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `mining_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `lease_area_ha` | `decimal(10,2)` | `YES` | `` | `NULL` |  |
| `surveyed_area_ha` | `decimal(10,2)` | `YES` | `` | `NULL` |  |
| `area_discrepancy_ha` | `decimal(10,2)` | `YES` | `` | `NULL` |  |
| `location` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `survey_date` | `date` | `YES` | `MUL` | `NULL` |  |
| `surveyor_user_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `survey_team_notes` | `text` | `YES` | `` | `NULL` |  |
| `instrument_model` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `instrument_serial_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `survey_status` | `varchar(100)` | `NO` | `` | `scheduled` |  |
| `status_notes` | `text` | `YES` | `` | `NULL` |  |
| `report_status` | `enum('pending','verified','dispatched')` | `NO` | `` | `pending` |  |
| `gtm_report_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `autocad_dwg_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `common_id` | `varchar(50)` | `YES` | `MUL` | `NULL` | Foreign Key |
| `master_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |

### Table: `districts`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `name` | `varchar(100)` | `NO` | `UNI` | `NULL` |  |
| `code` | `varchar(10)` | `YES` | `UNI` | `NULL` |  |
| `state` | `varchar(100)` | `NO` | `` | `Tamil Nadu` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `document_fields`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `folder_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `nature_of_work_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `required` | `tinyint(1)` | `NO` | `` | `1` |  |
| `sort_order` | `int(11)` | `NO` | `` | `0` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `drone_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `drone_survey_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_size` | `bigint(20) unsigned` | `YES` | `` | `NULL` |  |
| `status` | `varchar(30)` | `NO` | `` | `pending` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `drone_surveys`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `survey_no` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `lease_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `mining_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `lease_area` | `decimal(10,2)` | `YES` | `` | `NULL` |  |
| `location` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `flight_date` | `date` | `YES` | `MUL` | `NULL` |  |
| `drone_pilot_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `pilot_rpc_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `drone_uin_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `drone_model` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `altitude_meters` | `decimal(8,2)` | `YES` | `` | `NULL` |  |
| `gsd_cm_px` | `decimal(6,2)` | `YES` | `` | `NULL` |  |
| `extracted_volume_cbm` | `decimal(14,2)` | `YES` | `` | `NULL` |  |
| `survey_status` | `varchar(100)` | `NO` | `` | `scheduled` |  |
| `status_notes` | `text` | `YES` | `` | `NULL` |  |
| `deliverable_files_path` | `text` | `YES` | `` | `NULL` |  |
| `gtms_report_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `common_id` | `varchar(50)` | `YES` | `MUL` | `NULL` | Foreign Key |
| `master_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |

### Table: `ec_certificates`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `ec_ref_no` | `varchar(100)` | `NO` | `UNI` | `NULL` |  |
| `environment_project_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `lease_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `parivesh_app_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `parivesh_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `email_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `applicant_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `issue_date` | `date` | `NO` | `` | `NULL` |  |
| `expiry_date` | `date` | `NO` | `MUL` | `NULL` |  |
| `validity_years` | `int(11)` | `YES` | `` | `NULL` |  |
| `communication_type` | `enum('Grant','Rejection','ToR')` | `NO` | `` | `Grant` |  |
| `certificate_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `conditions_summary` | `text` | `YES` | `` | `NULL` |  |
| `status` | `varchar(100)` | `NO` | `` | `active` |  |
| `status_notes` | `text` | `YES` | `` | `NULL` |  |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |

### Table: `ec_compliance_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `ec_compliance_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder_category` | `enum('documents','site_analysis','report','parivesh_upload')` | `NO` | `` | `documents` |  |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `file_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_type` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_size` | `bigint(20) unsigned` | `YES` | `` | `NULL` |  |
| `is_mandatory` | `tinyint(1)` | `NO` | `` | `1` |  |
| `is_custom` | `tinyint(1)` | `NO` | `` | `0` |  |
| `status` | `enum('pending','uploaded','verified','rejected')` | `NO` | `` | `pending` |  |
| `review_note` | `text` | `YES` | `` | `NULL` |  |
| `uploaded_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `ec_compliances`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `compliance_no` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `primary_contact_person` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `primary_phone` | `varchar(25)` | `YES` | `` | `NULL` |  |
| `secondary_contact_person` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `secondary_phone` | `varchar(25)` | `YES` | `` | `NULL` |  |
| `environment_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `environment_project_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `ec_certificate_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `ec_certificate_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `ec_certificate_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `project_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `district_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `taluk_village` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `mineral_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `compliance_period` | `varchar(100)` | `NO` | `MUL` | `NULL` |  |
| `compliance_year` | `varchar(10)` | `NO` | `` | `2026` |  |
| `submission_due_date` | `date` | `YES` | `` | `NULL` |  |
| `submission_date` | `date` | `YES` | `` | `NULL` |  |
| `parivesh_app_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `parivesh_acknowledgement_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `parivesh_uploaded_date` | `date` | `YES` | `` | `NULL` |  |
| `nabl_lab_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `nabl_certificate_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `monitoring_date` | `date` | `YES` | `` | `NULL` |  |
| `status` | `varchar(100)` | `NO` | `` | `draft` |  |
| `status_notes` | `text` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `payment_notes` | `text` | `YES` | `` | `NULL` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `environment_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `environment_project_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `document_field_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `file_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_type` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_size` | `bigint(20) unsigned` | `YES` | `` | `NULL` |  |
| `status` | `enum('pending','uploaded','validated','approved','revision_required')` | `NO` | `` | `pending` |  |
| `review_note` | `text` | `YES` | `` | `NULL` |  |
| `reviewed_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `reviewed_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `uploaded_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `uploaded_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `environment_projects`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `project_code` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `mining_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `lease_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `category` | `enum('B1','B2')` | `NO` | `` | `B2` |  |
| `sub_category` | `varchar(50)` | `YES` | `` | `NULL` |  |
| `b1_stage` | `varchar(50)` | `YES` | `` | `sc1_prep` |  |
| `ppt_stage_1_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `ppt_stage_2_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `project_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `district_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `location` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `contact_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `secondary_contact_person` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `contact_phone` | `varchar(20)` | `YES` | `` | `NULL` |  |
| `secondary_phone` | `varchar(25)` | `YES` | `` | `NULL` |  |
| `contact_email` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `parivesh_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `email_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `public_hearing_date` | `date` | `YES` | `` | `NULL` |  |
| `public_hearing_minutes_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `status` | `varchar(100)` | `NO` | `` | `draft` |  |
| `status_notes` | `text` | `YES` | `` | `NULL` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `common_id` | `varchar(50)` | `YES` | `MUL` | `NULL` | Foreign Key |
| `master_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |

### Table: `environmental_activities`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `project_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `action` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `details` | `text` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `environmental_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `project_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `description` | `text` | `YES` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `original_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `status` | `varchar(255)` | `NO` | `` | `pending` |  |
| `review_note` | `text` | `YES` | `` | `NULL` |  |
| `uploaded_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `validated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `approved_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `environmental_projects`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `project_code` | `varchar(255)` | `NO` | `UNI` | `NULL` |  |
| `client_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `project_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `location` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `district` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `contact_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `contact_phone` | `varchar(20)` | `YES` | `` | `NULL` |  |
| `contact_email` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `sub_category` | `varchar(255)` | `NO` | `` | `B2` |  |
| `status` | `varchar(255)` | `NO` | `` | `draft` |  |
| `validated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `approved_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `archived_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `folders`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `module_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `name` | `varchar(100)` | `NO` | `` | `NULL` |  |
| `sort_order` | `int(11)` | `NO` | `` | `0` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `jobs`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `queue` | `varchar(255)` | `NO` | `MUL` | `NULL` |  |
| `payload` | `longtext` | `NO` | `` | `NULL` |  |
| `attempts` | `tinyint(3) unsigned` | `NO` | `` | `NULL` |  |
| `reserved_at` | `int(10) unsigned` | `YES` | `` | `NULL` |  |
| `available_at` | `int(10) unsigned` | `NO` | `` | `NULL` |  |
| `created_at` | `int(10) unsigned` | `NO` | `` | `NULL` |  |

### Table: `lease_application_minerals`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `lease_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `mineral_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `lease_applications`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `common_id` | `varchar(50)` | `YES` | `MUL` | `NULL` | Foreign Key |
| `application_no` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `district_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `category_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `other_category_rule` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `mineral_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `other_mineral_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `taluk` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `village` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `area_extent_ha` | `varchar(50)` | `YES` | `` | `NULL` |  |
| `area_extent_acres` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `start_date` | `date` | `YES` | `` | `NULL` |  |
| `end_date` | `date` | `YES` | `MUL` | `NULL` |  |
| `lease_period_years` | `int(11)` | `YES` | `` | `NULL` |  |
| `contact_person` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `secondary_contact_person` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `contact_mobile` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `secondary_contact_mobile` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `current_step` | `tinyint(4)` | `NO` | `` | `1` |  |
| `status` | `varchar(100)` | `NO` | `` | `draft` |  |
| `go_number` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `go_date` | `date` | `YES` | `` | `NULL` |  |
| `go_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `rejection_note` | `text` | `YES` | `` | `NULL` |  |
| `assigned_inspector_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `master_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |

### Table: `lease_categories`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `code` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `land_type` | `enum('Patta','Poramboke','Both')` | `NO` | `` | `Patta` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `is_custom` | `tinyint(1)` | `NO` | `` | `0` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `lease_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `lease_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `document_field_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `file_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_type` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_size` | `bigint(20) unsigned` | `YES` | `` | `NULL` |  |
| `status` | `enum('pending','uploaded','validated','approved','revision_required')` | `NO` | `` | `pending` |  |
| `review_note` | `text` | `YES` | `` | `NULL` |  |
| `reviewed_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `reviewed_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `uploaded_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `uploaded_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `lease_survey_numbers`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `lease_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `survey_no` | `varchar(100)` | `NO` | `MUL` | `NULL` |  |
| `extent_ha` | `decimal(10,4)` | `YES` | `` | `NULL` |  |
| `classification` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `pattadar_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `master_projects`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `master_application_id` | `varchar(50)` | `NO` | `UNI` | `NULL` | Foreign Key |
| `customer_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `district_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `taluk` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `village` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `survey_numbers` | `text` | `YES` | `` | `NULL` |  |
| `area_extent_ha` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `mineral_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `lease_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `dgps_survey_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `drone_survey_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `mining_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `environment_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `ppt_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `current_stage` | `varchar(255)` | `NO` | `MUL` | `lease` |  |
| `pipeline_status` | `varchar(255)` | `NO` | `MUL` | `in_progress` |  |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mimas_credentials`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `lease_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `user_id` | `varchar(255)` | `NO` | `` | `NULL` | Foreign Key |
| `password` | `text` | `NO` | `` | `NULL` |  |
| `email` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `contact_number` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `mimas_ack_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `ack_date` | `date` | `YES` | `` | `NULL` |  |
| `portal_status` | `varchar(50)` | `NO` | `` | `pending` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mineral_dispatches`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `mineral_stockpile_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `dispatch_date` | `timestamp` | `NO` | `` | `current_timestamp()` |  |
| `quantity` | `decimal(12,2)` | `NO` | `` | `NULL` |  |
| `vehicle_number` | `varchar(50)` | `NO` | `MUL` | `NULL` |  |
| `driver_name` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `destination` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `seigniorage_fee_inr` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `challan_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `status` | `enum('pending','approved','dispatched','cancelled')` | `NO` | `` | `dispatched` |  |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mineral_stock_entries`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `mineral_stockpile_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `entry_date` | `date` | `NO` | `` | `NULL` |  |
| `quantity` | `decimal(12,2)` | `NO` | `` | `NULL` |  |
| `source_type` | `enum('quarry_extraction','drone_volume_audit','manual_adjustment')` | `NO` | `` | `quarry_extraction` |  |
| `verified_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `remarks` | `text` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mineral_stockpiles`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `quarry_customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `lease_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `mineral_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `annual_permitted_quota` | `decimal(14,2)` | `NO` | `` | `NULL` |  |
| `current_stock_cbm` | `decimal(14,2)` | `NO` | `` | `0.00` |  |
| `total_dispatched_cbm` | `decimal(14,2)` | `NO` | `` | `0.00` |  |
| `unit` | `varchar(20)` | `NO` | `` | `CBM` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `minerals`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `name` | `varchar(100)` | `NO` | `UNI` | `NULL` |  |
| `category` | `enum('Major','Minor')` | `NO` | `` | `Minor` |  |
| `default_unit` | `varchar(20)` | `NO` | `` | `CBM` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mining_application_minerals`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `mining_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `mineral_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mining_applications`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `common_id` | `varchar(50)` | `YES` | `MUL` | `NULL` | Foreign Key |
| `nature_of_work_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `other_nature_of_work_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `application_no` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `lease_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `parent_plan_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `applicant_type_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `district_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `mineral_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `other_mineral_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `plan_type_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `taluk` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `village` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `survey_numbers_text` | `text` | `YES` | `` | `NULL` |  |
| `area_extent_ha` | `varchar(50)` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `start_date` | `date` | `YES` | `` | `NULL` |  |
| `end_date` | `date` | `YES` | `MUL` | `NULL` |  |
| `validity_years` | `int(11)` | `NO` | `` | `5` |  |
| `stage` | `enum('6.1','6.2','6.3','6.4','6.5','6.6')` | `NO` | `` | `6.1` |  |
| `current_step` | `tinyint(3) unsigned` | `NO` | `` | `1` |  |
| `status` | `varchar(100)` | `NO` | `` | `draft` |  |
| `status_notes` | `text` | `YES` | `` | `NULL` |  |
| `rqp_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `rqp_reg_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `safety_distance_meters` | `decimal(8,2)` | `YES` | `` | `NULL` |  |
| `assigned_inspector_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `approval_order_no` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `approval_date` | `date` | `YES` | `` | `NULL` |  |
| `approval_file` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `kml_file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `master_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |

### Table: `mining_boundary_points`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `mining_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `pillar_id` | `varchar(50)` | `NO` | `` | `NULL` | Foreign Key |
| `latitude` | `decimal(11,8)` | `NO` | `` | `NULL` |  |
| `longitude` | `decimal(11,8)` | `NO` | `` | `NULL` |  |
| `elevation` | `decimal(8,2)` | `YES` | `` | `NULL` |  |
| `remarks` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mining_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `mining_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `document_field_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `file_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_type` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_size` | `bigint(20) unsigned` | `YES` | `` | `NULL` |  |
| `status` | `enum('pending','uploaded','validated','approved','revision_required')` | `NO` | `` | `pending` |  |
| `review_note` | `text` | `YES` | `` | `NULL` |  |
| `reviewed_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `reviewed_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `uploaded_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `uploaded_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `mining_production_schedules`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `mining_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `year_number` | `tinyint(4)` | `NO` | `` | `NULL` |  |
| `production_target` | `decimal(12,2)` | `NO` | `` | `NULL` |  |
| `waste_removal` | `decimal(12,2)` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `model_has_permissions`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `permission_id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Foreign Key |
| `model_type` | `varchar(255)` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `model_id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Foreign Key |

### Table: `model_has_roles`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `role_id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Foreign Key |
| `model_type` | `varchar(255)` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `model_id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Foreign Key |

### Table: `modules`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `code` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `name` | `varchar(100)` | `NO` | `` | `NULL` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `nature_of_works`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `notifications`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `char(36)` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `type` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `notifiable_type` | `varchar(255)` | `NO` | `MUL` | `NULL` |  |
| `notifiable_id` | `bigint(20) unsigned` | `NO` | `` | `NULL` | Foreign Key |
| `data` | `text` | `NO` | `` | `NULL` |  |
| `read_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `payment_receipts`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `receipt_number` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `quotation_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `application_type` | `varchar(50)` | `YES` | `MUL` | `NULL` |  |
| `application_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `amount_paid` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `balance_due` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `previous_paid` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `payment_mode` | `enum('Cash','Cheque','NEFT/RTGS','UPI/GPay','Other')` | `NO` | `` | `Cash` |  |
| `bank_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `reference_number` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `transaction_date` | `date` | `NO` | `MUL` | `NULL` |  |
| `notes` | `text` | `YES` | `` | `NULL` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `permissions`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `name` | `varchar(255)` | `NO` | `MUL` | `NULL` |  |
| `guard_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `pipeline_movements`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `master_project_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `from_department` | `varchar(50)` | `NO` | `MUL` | `NULL` |  |
| `to_department` | `varchar(50)` | `NO` | `` | `NULL` |  |
| `action_type` | `varchar(50)` | `NO` | `MUL` | `NULL` |  |
| `priority` | `varchar(20)` | `NO` | `MUL` | `normal` |  |
| `mistake_category` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `reason` | `text` | `YES` | `` | `NULL` |  |
| `rectification_notes` | `text` | `YES` | `` | `NULL` |  |
| `officer_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `officer_name` | `varchar(150)` | `YES` | `` | `NULL` |  |
| `metadata` | `longtext` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `plan_types`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `name` | `varchar(100)` | `NO` | `UNI` | `NULL` |  |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `ppt_agendas`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `ppt_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `committee_type` | `enum('SEAC','SEIAA')` | `NO` | `` | `SEAC` |  |
| `meeting_no` | `varchar(50)` | `NO` | `` | `NULL` |  |
| `item_no` | `varchar(50)` | `NO` | `` | `NULL` |  |
| `meeting_date` | `date` | `NO` | `MUL` | `NULL` |  |
| `agenda_pdf` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `mom_pdf` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `outcome` | `enum('Recommended','Query_EDS','Query_ADS','Rejected')` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `ppt_applications`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `application_no` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `environment_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `presentation_stage` | `varchar(50)` | `NO` | `` | `tor_presentation` |  |
| `project_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `district_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `taluk_village` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `mineral_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `parivesh_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `email_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `status` | `varchar(100)` | `NO` | `` | `draft` |  |
| `status_notes` | `text` | `YES` | `` | `NULL` |  |
| `rqp_attending` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `company_rep_attending` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `rep_mobile` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `rep_secondary_mobile` | `varchar(25)` | `YES` | `` | `NULL` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `product_value` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `paid_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `pending_amount` | `decimal(12,2)` | `YES` | `` | `0.00` |  |
| `payment_status` | `enum('pending','partial','paid')` | `NO` | `` | `pending` |  |
| `common_id` | `varchar(50)` | `YES` | `MUL` | `NULL` | Foreign Key |
| `master_project_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |

### Table: `ppt_documents`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `ppt_application_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `folder_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `document_field_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `document_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `file_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_path` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_type` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `file_size` | `bigint(20) unsigned` | `YES` | `` | `NULL` |  |
| `status` | `enum('pending','uploaded','validated','approved','revision_required')` | `NO` | `` | `pending` |  |
| `review_note` | `text` | `YES` | `` | `NULL` |  |
| `reviewed_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `uploaded_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `product_stocks`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `product_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `total_stock` | `int(11)` | `NO` | `` | `0` |  |
| `available_stock` | `int(11)` | `NO` | `` | `0` |  |
| `sale_stock` | `int(11)` | `NO` | `` | `0` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `products`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `bar_code` | `varchar(255)` | `YES` | `UNI` | `NULL` |  |
| `pro_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `gst` | `decimal(5,2)` | `NO` | `` | `0.00` |  |
| `cast_per` | `decimal(10,2)` | `NO` | `` | `NULL` |  |
| `mrp` | `decimal(10,2)` | `NO` | `` | `NULL` |  |
| `unit` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `qty` | `int(11)` | `NO` | `` | `0` |  |
| `discount_1` | `decimal(5,2)` | `NO` | `` | `0.00` |  |
| `discount_2` | `decimal(5,2)` | `NO` | `` | `0.00` |  |
| `discount_3` | `decimal(5,2)` | `NO` | `` | `0.00` |  |
| `cat_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `branch_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `delete_status` | `tinyint(4)` | `NO` | `` | `0` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `project_flows`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `flowable_type` | `varchar(100)` | `NO` | `MUL` | `NULL` |  |
| `flowable_id` | `bigint(20) unsigned` | `NO` | `` | `NULL` | Foreign Key |
| `step_code` | `varchar(20)` | `NO` | `` | `NULL` |  |
| `step_name` | `varchar(100)` | `NO` | `` | `NULL` |  |
| `status` | `enum('pending','in_progress','passed','rejected')` | `NO` | `` | `pending` |  |
| `note` | `text` | `YES` | `` | `NULL` |  |
| `handled_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` |  |
| `handled_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `quotation_items`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `quotation_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `service_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `sac_code` | `varchar(50)` | `YES` | `` | `NULL` |  |
| `description` | `text` | `YES` | `` | `NULL` |  |
| `quantity` | `decimal(10,2)` | `NO` | `` | `1.00` |  |
| `unit` | `varchar(50)` | `NO` | `` | `Nos` |  |
| `unit_rate` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `subtotal` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `quotations`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `quotation_number` | `varchar(50)` | `NO` | `UNI` | `NULL` |  |
| `customer_id` | `bigint(20) unsigned` | `NO` | `MUL` | `NULL` | Foreign Key |
| `lease_application_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `customer_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `company_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `subject` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `phone` | `varchar(50)` | `YES` | `` | `NULL` |  |
| `email` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `gst_number` | `varchar(50)` | `YES` | `` | `NULL` |  |
| `address` | `text` | `YES` | `` | `NULL` |  |
| `quarry_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `district_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `taluk` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `village` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `survey_numbers` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `area_extent_ha` | `decimal(10,4)` | `YES` | `` | `NULL` |  |
| `mineral_name` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `scope_sections` | `longtext` | `YES` | `` | `NULL` |  |
| `subtotal` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `tax_rate` | `decimal(5,2)` | `NO` | `` | `18.00` |  |
| `tax_amount` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `total_amount` | `decimal(12,2)` | `NO` | `` | `0.00` |  |
| `validity_days` | `int(11)` | `NO` | `` | `30` |  |
| `payment_terms` | `text` | `YES` | `` | `NULL` |  |
| `milestones` | `longtext` | `YES` | `` | `NULL` |  |
| `exclusions` | `text` | `YES` | `` | `NULL` |  |
| `authorized_signatory` | `varchar(255)` | `YES` | `` | `Dr. S. Karuppannan, M.Sc., Ph.D.` |  |
| `bank_account_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `notes` | `text` | `YES` | `` | `NULL` |  |
| `status` | `enum('draft','sent','accepted','rejected','converted')` | `NO` | `` | `draft` |  |
| `branch_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Multi-Tenancy Scope Key |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `deleted_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `role_has_permissions`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `permission_id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Foreign Key |
| `role_id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Foreign Key |

### Table: `roles`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `name` | `varchar(255)` | `NO` | `MUL` | `NULL` |  |
| `guard_name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `created_by` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Audit / User Creator |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `units`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `units` | `varchar(100)` | `NO` | `UNI` | `NULL` |  |
| `delete_status` | `tinyint(4)` | `NO` | `` | `0` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |

### Table: `users`
| Column | Type | Nullable | Key | Default | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | `NO` | `PRI` | `NULL` | Primary Key (Auto-Increment) |
| `user_code` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `user_id` | `bigint(20) unsigned` | `YES` | `MUL` | `NULL` | Foreign Key |
| `name` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `email` | `varchar(255)` | `NO` | `UNI` | `NULL` |  |
| `image` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `mobile_num` | `varchar(15)` | `YES` | `` | `NULL` |  |
| `email_verified_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `password` | `varchar(255)` | `NO` | `` | `NULL` |  |
| `show_password` | `varchar(255)` | `YES` | `` | `NULL` |  |
| `remember_token` | `varchar(100)` | `YES` | `` | `NULL` |  |
| `created_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `updated_at` | `timestamp` | `YES` | `` | `NULL` |  |
| `role_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Foreign Key |
| `branch_id` | `bigint(20) unsigned` | `YES` | `` | `NULL` | Multi-Tenancy Scope Key |
| `status` | `tinyint(4)` | `NO` | `` | `1` |  |

