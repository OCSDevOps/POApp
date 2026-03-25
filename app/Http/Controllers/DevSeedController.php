<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Development-only controller to seed the database via HTTP.
 * Only available when APP_DEBUG=true.
 * This exists because artisan commands hang on this project.
 */
class DevSeedController extends Controller
{
    /**
     * Create all missing database tables, columns, and views.
     * GET /_dev/create-tables
     */
    public function createTables()
    {
        if (!config('app.debug')) {
            abort(403, 'Only available in debug mode');
        }

        set_time_limit(300);
        $messages = [];
        $created = 0;
        $skipped = 0;
        $errors = 0;

        $pdo = DB::connection()->getPdo();

        // Helper to check if table exists
        $tableExists = function($name) use ($pdo) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '$name'");
            return $stmt->fetchColumn() > 0;
        };

        // Helper to check if column exists
        $columnExists = function($table, $column) use ($pdo) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = '$table' AND column_name = '$column'");
            return $stmt->fetchColumn() > 0;
        };

        // Helper to create table
        $createTable = function($name, $sql) use ($tableExists, &$messages, &$created, &$skipped, &$errors, $pdo) {
            if ($tableExists($name)) {
                $messages[] = "[SKIP] $name already exists";
                $skipped++;
                return;
            }
            try {
                $pdo->exec($sql);
                $messages[] = "[OK]   $name created";
                $created++;
            } catch (\Throwable $e) {
                $messages[] = "[FAIL] $name: " . $e->getMessage();
                $errors++;
            }
        };

        // Helper to add column
        $addColumn = function($table, $column, $definition) use ($columnExists, &$messages, $pdo) {
            if ($columnExists($table, $column)) {
                $messages[] = "[SKIP] $table.$column already exists";
                return;
            }
            try {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
                $messages[] = "[OK]   $table.$column added";
            } catch (\Throwable $e) {
                $messages[] = "[FAIL] $table.$column: " . $e->getMessage();
            }
        };

        $messages[] = '========================================';
        $messages[] = '  CREATING MISSING TABLES';
        $messages[] = '========================================';
        $messages[] = '';

        // ── budget_master ──
        $createTable('budget_master', "
            CREATE TABLE budget_master (
                budget_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                budget_project_id BIGINT NULL,
                budget_cost_code_id BIGINT NULL,
                budget_original_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_revised_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_committed_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_spent_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_remaining_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_fiscal_year VARCHAR(10) NULL,
                budget_notes TEXT NULL,
                budget_status TINYINT NOT NULL DEFAULT 1,
                budget_created_by BIGINT NULL,
                budget_created_at DATETIME NULL,
                budget_modified_by BIGINT NULL,
                budget_modified_at DATETIME NULL,
                procore_budget_id BIGINT NULL,
                budget_change_orders_total DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_committed DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_actual DECIMAL(18,2) NOT NULL DEFAULT 0,
                budget_warning_threshold DECIMAL(18,2) NULL DEFAULT 80.00,
                budget_critical_threshold DECIMAL(18,2) NULL DEFAULT 95.00,
                committed DECIMAL(18,2) NOT NULL DEFAULT 0,
                actual DECIMAL(18,2) NOT NULL DEFAULT 0,
                warning_notification_sent TINYINT NOT NULL DEFAULT 0,
                critical_notification_sent TINYINT NOT NULL DEFAULT 0,
                original_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                variance DECIMAL(18,2) NOT NULL DEFAULT 0,
                company_id BIGINT UNSIGNED NULL,
                INDEX idx_budget_project (budget_project_id),
                INDEX idx_budget_company (company_id),
                INDEX idx_budget_costcode (budget_cost_code_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── approval_workflows ──
        $createTable('approval_workflows', "
            CREATE TABLE approval_workflows (
                workflow_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NULL,
                workflow_name VARCHAR(255) NULL,
                workflow_type VARCHAR(50) NOT NULL,
                approval_level INT NOT NULL DEFAULT 1,
                amount_threshold_min DECIMAL(18,2) NULL DEFAULT 0,
                amount_threshold_max DECIMAL(18,2) NULL,
                approver_user_ids JSON NULL,
                approval_logic VARCHAR(50) NULL DEFAULT 'any',
                is_active TINYINT NOT NULL DEFAULT 1,
                sort_order INT NOT NULL DEFAULT 0,
                workflow_notes TEXT NULL,
                approver_roles JSON NULL,
                project_id BIGINT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_aw_company (company_id),
                INDEX idx_aw_type (workflow_type),
                INDEX idx_aw_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── eq_master ──
        $createTable('eq_master', "
            CREATE TABLE eq_master (
                eq_id INT AUTO_INCREMENT PRIMARY KEY,
                eqm_asset_name VARCHAR(191) NOT NULL,
                eqm_asset_description VARCHAR(1000) NULL,
                eqm_asset_type VARCHAR(100) NULL,
                eqm_asset_tag VARCHAR(191) NULL,
                eqm_asset_picture VARCHAR(500) NULL,
                eqm_asset_condition VARCHAR(100) NULL,
                eqm_category VARCHAR(191) NULL,
                eqm_status VARCHAR(50) NULL,
                eqm_existing_reading DECIMAL(18,2) NULL,
                eqm_estimate_usage DECIMAL(18,2) NULL,
                eqm_remaining_life DECIMAL(18,2) NULL,
                eqm_location VARCHAR(191) NULL,
                eqm_supplier INT NULL,
                eqm_serial VARCHAR(191) NULL,
                eqm_year VARCHAR(50) NULL,
                eqm_license_plate VARCHAR(191) NULL,
                eqm_current_operator INT NULL,
                eqm_purchase_price DECIMAL(18,2) NULL,
                eqm_purchase_date DATE NULL,
                eqm_current_value DECIMAL(18,2) NULL,
                eqm_brand VARCHAR(191) NULL,
                eqm_model VARCHAR(191) NULL,
                eqm_war_expiry_date DATE NULL,
                eqm_dep_method VARCHAR(100) NULL,
                eqm_rental_total_value DECIMAL(18,2) NULL,
                eqm_rental_insurance VARCHAR(191) NULL,
                eqm_rental_insurance_amt DECIMAL(18,2) NULL,
                eqm_created_date DATETIME NULL,
                status INT NOT NULL DEFAULT 1,
                company_id INT NULL,
                INDEX idx_eq_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── permission_master ──
        $createTable('permission_master', "
            CREATE TABLE permission_master (
                pt_id INT AUTO_INCREMENT PRIMARY KEY,
                pt_template_name VARCHAR(191) NOT NULL,
                pt_template_users TEXT NULL,
                pt_t_porder INT NOT NULL DEFAULT 0,
                pt_t_rorder INT NOT NULL DEFAULT 0,
                pt_t_rcorder INT NOT NULL DEFAULT 0,
                pt_t_rfq INT NOT NULL DEFAULT 0,
                pt_m_item INT NOT NULL DEFAULT 0,
                pt_m_uom INT NOT NULL DEFAULT 0,
                pt_m_costcode INT NOT NULL DEFAULT 0,
                pt_m_projects INT NOT NULL DEFAULT 0,
                pt_m_suppliers INT NOT NULL DEFAULT 0,
                pt_m_taxgroup INT NOT NULL DEFAULT 0,
                pt_m_budget INT NOT NULL DEFAULT 0,
                pt_m_email INT NOT NULL DEFAULT 0,
                pt_i_item INT NOT NULL DEFAULT 0,
                pt_i_itemp INT NOT NULL DEFAULT 0,
                pt_i_supplierc INT NOT NULL DEFAULT 0,
                pt_e_eq INT NOT NULL DEFAULT 0,
                pt_e_eqm INT NOT NULL DEFAULT 0,
                pt_e_checklist INT NOT NULL DEFAULT 0,
                pt_a_user INT NOT NULL DEFAULT 0,
                pt_a_permissions INT NOT NULL DEFAULT 0,
                pt_a_cinfo INT NOT NULL DEFAULT 0,
                pt_a_procore INT NOT NULL DEFAULT 0,
                created_date DATETIME NULL,
                status INT NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── procore_auth ──
        $createTable('procore_auth', "
            CREATE TABLE procore_auth (
                id INT AUTO_INCREMENT PRIMARY KEY,
                CLIENT_ID VARCHAR(255) NULL,
                SECRET_KEY VARCHAR(255) NULL,
                COMPANY_ID VARCHAR(255) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── procore_sync_log ──
        $createTable('procore_sync_log', "
            CREATE TABLE procore_sync_log (
                sync_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                sync_type VARCHAR(50) NOT NULL,
                sync_direction VARCHAR(20) NOT NULL DEFAULT 'inbound',
                sync_entity_id BIGINT NULL,
                sync_procore_id BIGINT NULL,
                sync_status VARCHAR(20) NOT NULL DEFAULT 'pending',
                sync_message TEXT NULL,
                sync_request_data TEXT NULL,
                sync_response_data TEXT NULL,
                sync_created_at DATETIME NULL,
                sync_created_by BIGINT NULL,
                INDEX idx_psl_type (sync_type),
                INDEX idx_psl_status (sync_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── contract_master ──
        $createTable('contract_master', "
            CREATE TABLE contract_master (
                contract_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                contract_number VARCHAR(50) NOT NULL,
                contract_title VARCHAR(255) NOT NULL,
                contract_description TEXT NULL,
                contract_project_id BIGINT NOT NULL,
                contract_supplier_id BIGINT NOT NULL,
                contract_cost_code_id BIGINT NULL,
                contract_original_value DECIMAL(18,2) NOT NULL DEFAULT 0,
                contract_approved_cos DECIMAL(18,2) NOT NULL DEFAULT 0,
                contract_pending_cos DECIMAL(18,2) NOT NULL DEFAULT 0,
                contract_invoiced_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                contract_paid_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                contract_retention_pct DECIMAL(5,2) NOT NULL DEFAULT 0,
                contract_retention_held DECIMAL(18,2) NOT NULL DEFAULT 0,
                contract_retention_released DECIMAL(18,2) NOT NULL DEFAULT 0,
                contract_start_date DATE NULL,
                contract_end_date DATE NULL,
                contract_status TINYINT NOT NULL DEFAULT 1,
                contract_scope TEXT NULL,
                contract_terms TEXT NULL,
                contract_created_by BIGINT NULL,
                contract_created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                contract_modified_by BIGINT NULL,
                contract_modified_at DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                procore_contract_id BIGINT NULL,
                INDEX idx_cm_project (contract_project_id),
                INDEX idx_cm_supplier (contract_supplier_id),
                INDEX idx_cm_company (company_id),
                INDEX idx_cm_status (contract_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── contract_change_orders ──
        $createTable('contract_change_orders', "
            CREATE TABLE contract_change_orders (
                cco_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                cco_number VARCHAR(50) NOT NULL,
                contract_id BIGINT NOT NULL,
                cco_amount DECIMAL(18,2) NOT NULL,
                cco_description TEXT NOT NULL,
                cco_reason VARCHAR(500) NULL,
                cco_status VARCHAR(30) NOT NULL DEFAULT 'draft',
                submitted_at DATETIME NULL,
                approved_by BIGINT NULL,
                approved_at DATETIME NULL,
                rejection_reason VARCHAR(500) NULL,
                created_by BIGINT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_cco_contract (contract_id),
                INDEX idx_cco_company (company_id),
                INDEX idx_cco_status (cco_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── contract_documents ──
        $createTable('contract_documents', "
            CREATE TABLE contract_documents (
                cdoc_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                cdoc_contract_id BIGINT NOT NULL,
                cdoc_original_name VARCHAR(255) NOT NULL,
                cdoc_path VARCHAR(500) NOT NULL,
                cdoc_mime VARCHAR(100) NULL,
                cdoc_size BIGINT NULL,
                cdoc_type VARCHAR(30) NOT NULL DEFAULT 'other',
                cdoc_description VARCHAR(500) NULL,
                cdoc_createby BIGINT NULL,
                cdoc_createdate DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                cdoc_status TINYINT NOT NULL DEFAULT 1,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_cdoc_contract (cdoc_contract_id),
                INDEX idx_cdoc_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── supplier_compliance ──
        $createTable('supplier_compliance', "
            CREATE TABLE supplier_compliance (
                compliance_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                compliance_supplier_id BIGINT NOT NULL,
                compliance_type VARCHAR(50) NOT NULL,
                compliance_name VARCHAR(255) NOT NULL,
                compliance_number VARCHAR(100) NULL,
                compliance_issuer VARCHAR(255) NULL,
                compliance_amount DECIMAL(18,2) NULL,
                compliance_issue_date DATE NULL,
                compliance_expiry_date DATE NULL,
                compliance_warning_days INT NOT NULL DEFAULT 30,
                compliance_document_path VARCHAR(500) NULL,
                compliance_document_name VARCHAR(255) NULL,
                compliance_status TINYINT NOT NULL DEFAULT 1,
                compliance_required TINYINT NOT NULL DEFAULT 1,
                compliance_contract_id BIGINT NULL,
                compliance_notes TEXT NULL,
                compliance_created_by BIGINT NULL,
                compliance_created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                compliance_modified_by BIGINT NULL,
                compliance_modified_at DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_sc_supplier (compliance_supplier_id),
                INDEX idx_sc_expiry (compliance_expiry_date),
                INDEX idx_sc_company (company_id),
                INDEX idx_sc_type (compliance_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── contract_invoices ──
        $createTable('contract_invoices', "
            CREATE TABLE contract_invoices (
                cinv_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                cinv_contract_id BIGINT NOT NULL,
                cinv_number VARCHAR(50) NOT NULL,
                cinv_description VARCHAR(500) NULL,
                cinv_gross_amount DECIMAL(18,2) NOT NULL,
                cinv_retention_held DECIMAL(18,2) NOT NULL DEFAULT 0,
                cinv_net_amount DECIMAL(18,2) NOT NULL,
                cinv_paid_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                cinv_invoice_date DATE NOT NULL,
                cinv_due_date DATE NULL,
                cinv_paid_date DATE NULL,
                cinv_period_from DATE NULL,
                cinv_period_to DATE NULL,
                cinv_status TINYINT NOT NULL DEFAULT 1,
                cinv_created_by BIGINT NULL,
                cinv_created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                cinv_modified_by BIGINT NULL,
                cinv_modified_at DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_cinv_contract (cinv_contract_id),
                INDEX idx_cinv_company (company_id),
                INDEX idx_cinv_status (cinv_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── ai_settings ──
        $createTable('ai_settings', "
            CREATE TABLE ai_settings (
                ai_setting_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                ai_provider VARCHAR(50) NOT NULL DEFAULT 'openai',
                api_key VARCHAR(500) NULL,
                model_name VARCHAR(100) NOT NULL DEFAULT 'gpt-4o',
                max_tokens INT NOT NULL DEFAULT 4096,
                temperature DECIMAL(3,2) NOT NULL DEFAULT 0.20,
                is_active TINYINT NOT NULL DEFAULT 0,
                ai_createby BIGINT NULL,
                ai_createdate DATETIME NULL,
                ai_modifyby BIGINT NULL,
                ai_modifydate DATETIME NULL,
                INDEX idx_ai_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── takeoff_master ──
        $createTable('takeoff_master', "
            CREATE TABLE takeoff_master (
                to_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                to_number VARCHAR(50) NOT NULL,
                to_project_id BIGINT NOT NULL,
                to_title VARCHAR(250) NOT NULL,
                to_description TEXT NULL,
                to_status TINYINT NOT NULL DEFAULT 1,
                to_total_items INT NOT NULL DEFAULT 0,
                to_subtotal DECIMAL(18,2) NOT NULL DEFAULT 0,
                to_tax_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                to_total_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
                to_notes TEXT NULL,
                to_finalized_by BIGINT NULL,
                to_finalized_at DATETIME NULL,
                to_createby BIGINT NULL,
                to_createdate DATETIME NULL,
                to_modifyby BIGINT NULL,
                to_modifydate DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                UNIQUE KEY uq_takeoff_number (to_number),
                INDEX idx_to_project (to_project_id),
                INDEX idx_to_company (company_id),
                INDEX idx_to_status (to_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── takeoff_details ──
        $createTable('takeoff_details', "
            CREATE TABLE takeoff_details (
                tod_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                tod_takeoff_id BIGINT NOT NULL,
                tod_item_code VARCHAR(50) NULL,
                tod_description VARCHAR(500) NOT NULL,
                tod_quantity DECIMAL(18,4) NOT NULL DEFAULT 0,
                tod_uom_id BIGINT NULL,
                tod_unit_price DECIMAL(18,2) NOT NULL DEFAULT 0,
                tod_subtotal DECIMAL(18,2) NOT NULL DEFAULT 0,
                tod_cost_code_id BIGINT NULL,
                tod_source VARCHAR(20) NOT NULL DEFAULT 'manual',
                tod_ai_confidence DECIMAL(5,2) NULL,
                tod_notes TEXT NULL,
                tod_status TINYINT NOT NULL DEFAULT 1,
                tod_createdate DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_tod_takeoff (tod_takeoff_id),
                INDEX idx_tod_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── takeoff_drawings ──
        $createTable('takeoff_drawings', "
            CREATE TABLE takeoff_drawings (
                tdr_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                tdr_takeoff_id BIGINT NOT NULL,
                tdr_original_name VARCHAR(500) NOT NULL,
                tdr_path VARCHAR(500) NOT NULL,
                tdr_mime VARCHAR(100) NOT NULL,
                tdr_size BIGINT NOT NULL DEFAULT 0,
                tdr_page_count INT NULL,
                tdr_ai_status VARCHAR(20) NOT NULL DEFAULT 'pending',
                tdr_ai_processed_at DATETIME NULL,
                tdr_ai_raw_response TEXT NULL,
                tdr_ai_error VARCHAR(1000) NULL,
                tdr_createby BIGINT NULL,
                tdr_createdate DATETIME NULL,
                tdr_status TINYINT NOT NULL DEFAULT 1,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_tdr_takeoff (tdr_takeoff_id),
                INDEX idx_tdr_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── accounting_integrations ──
        $createTable('accounting_integrations', "
            CREATE TABLE accounting_integrations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                integration_type VARCHAR(50) NOT NULL,
                client_id VARCHAR(255) NULL,
                client_secret VARCHAR(500) NULL,
                access_token TEXT NULL,
                refresh_token TEXT NULL,
                token_expires_at DATETIME NULL,
                settings JSON NULL,
                is_active TINYINT NOT NULL DEFAULT 0,
                auto_sync_purchase_orders TINYINT NOT NULL DEFAULT 0,
                auto_sync_vendors TINYINT NOT NULL DEFAULT 0,
                auto_sync_items TINYINT NOT NULL DEFAULT 0,
                last_sync_at DATETIME NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_ai_company (company_id),
                INDEX idx_ai_type (integration_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── po_template_master ──
        $createTable('po_template_master', "
            CREATE TABLE po_template_master (
                pot_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                pot_name VARCHAR(255) NOT NULL,
                pot_description TEXT NULL,
                pot_supplier_id BIGINT NULL,
                pot_project_id BIGINT NULL,
                pot_terms TEXT NULL,
                pot_delivery_notes TEXT NULL,
                pot_is_active TINYINT NOT NULL DEFAULT 1,
                pot_created_by BIGINT NULL,
                pot_created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                pot_modified_by BIGINT NULL,
                pot_modified_at DATETIME NULL,
                company_id BIGINT UNSIGNED NULL,
                INDEX idx_pot_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── project_roles ──
        $createTable('project_roles', "
            CREATE TABLE project_roles (
                role_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NULL,
                project_id BIGINT NULL,
                user_id BIGINT NULL,
                role_name VARCHAR(50) NOT NULL DEFAULT 'Staff',
                can_create_po TINYINT NOT NULL DEFAULT 0,
                can_approve_po TINYINT NOT NULL DEFAULT 0,
                can_create_budget_co TINYINT NOT NULL DEFAULT 0,
                can_approve_budget_co TINYINT NOT NULL DEFAULT 0,
                can_override_budget TINYINT NOT NULL DEFAULT 0,
                approval_limit DECIMAL(18,2) NULL DEFAULT 0,
                is_active TINYINT NOT NULL DEFAULT 1,
                notes TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_pr_company (company_id),
                INDEX idx_pr_project (project_id),
                INDEX idx_pr_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_calendars ──
        $createTable('schedule_calendars', "
            CREATE TABLE schedule_calendars (
                cal_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                cal_project_id BIGINT NULL,
                cal_name VARCHAR(200) NOT NULL,
                cal_timezone VARCHAR(100) NOT NULL DEFAULT 'America/New_York',
                cal_work_week VARCHAR(50) NOT NULL DEFAULT 'Mon,Tue,Wed,Thu,Fri',
                cal_work_start VARCHAR(10) NOT NULL DEFAULT '07:00',
                cal_work_end VARCHAR(10) NOT NULL DEFAULT '15:30',
                cal_is_default TINYINT NOT NULL DEFAULT 0,
                cal_status TINYINT NOT NULL DEFAULT 1,
                cal_createby BIGINT NULL,
                cal_createdate DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                cal_modifyby BIGINT NULL,
                cal_modifydate DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_cal_company (company_id),
                INDEX idx_cal_project (cal_project_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_calendar_exceptions ──
        $createTable('schedule_calendar_exceptions', "
            CREATE TABLE schedule_calendar_exceptions (
                cex_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                cex_calendar_id BIGINT NOT NULL,
                cex_date DATE NOT NULL,
                cex_type VARCHAR(20) NOT NULL DEFAULT 'holiday',
                cex_name VARCHAR(200) NULL,
                cex_work_start VARCHAR(10) NULL,
                cex_work_end VARCHAR(10) NULL,
                INDEX idx_cex_calendar (cex_calendar_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_wbs_nodes ──
        $createTable('schedule_wbs_nodes', "
            CREATE TABLE schedule_wbs_nodes (
                wbs_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                wbs_project_id BIGINT NOT NULL,
                wbs_parent_id BIGINT NULL,
                wbs_code VARCHAR(50) NOT NULL,
                wbs_name VARCHAR(250) NOT NULL,
                wbs_sort_order INT NOT NULL DEFAULT 0,
                wbs_status TINYINT NOT NULL DEFAULT 1,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_wbs_project (wbs_project_id),
                INDEX idx_wbs_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_activities ──
        $createTable('schedule_activities', "
            CREATE TABLE schedule_activities (
                act_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                act_project_id BIGINT NOT NULL,
                act_wbs_id BIGINT NULL,
                act_name VARCHAR(500) NOT NULL,
                act_description TEXT NULL,
                act_type VARCHAR(20) NOT NULL DEFAULT 'TASK',
                act_duration_minutes INT NOT NULL DEFAULT 0,
                act_calendar_id BIGINT NULL,
                act_status VARCHAR(20) NOT NULL DEFAULT 'NOT_STARTED',
                act_percent_complete DECIMAL(5,2) NOT NULL DEFAULT 0,
                act_is_locked TINYINT NOT NULL DEFAULT 0,
                act_priority INT NOT NULL DEFAULT 500,
                act_constraint_type VARCHAR(10) NOT NULL DEFAULT 'NONE',
                act_constraint_date DATETIME NULL,
                act_early_start DATETIME NULL,
                act_early_finish DATETIME NULL,
                act_late_start DATETIME NULL,
                act_late_finish DATETIME NULL,
                act_total_float_minutes INT NULL,
                act_free_float_minutes INT NULL,
                act_is_critical TINYINT NOT NULL DEFAULT 0,
                act_driving_predecessor_id BIGINT NULL,
                act_driving_constraint_id BIGINT NULL,
                act_sort_order INT NOT NULL DEFAULT 0,
                act_color VARCHAR(20) NULL,
                act_createby BIGINT NULL,
                act_createdate DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                act_modifyby BIGINT NULL,
                act_modifydate DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_act_project (act_project_id),
                INDEX idx_act_wbs (act_wbs_id),
                INDEX idx_act_company (company_id),
                INDEX idx_act_status (act_project_id, act_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_dependencies ──
        $createTable('schedule_dependencies', "
            CREATE TABLE schedule_dependencies (
                dep_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                dep_project_id BIGINT NOT NULL,
                dep_predecessor_id BIGINT NOT NULL,
                dep_successor_id BIGINT NOT NULL,
                dep_type VARCHAR(2) NOT NULL DEFAULT 'FS',
                dep_lag_minutes INT NOT NULL DEFAULT 0,
                dep_lag_calendar_mode VARCHAR(20) NOT NULL DEFAULT 'SUCCESSOR',
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_dep_project (dep_project_id),
                INDEX idx_dep_pred (dep_predecessor_id),
                INDEX idx_dep_succ (dep_successor_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_activity_actuals ──
        $createTable('schedule_activity_actuals', "
            CREATE TABLE schedule_activity_actuals (
                aca_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                aca_activity_id BIGINT NOT NULL,
                aca_actual_start DATETIME NULL,
                aca_actual_finish DATETIME NULL,
                aca_remaining_duration_minutes INT NULL,
                aca_note TEXT NULL,
                aca_updated_by BIGINT NULL,
                aca_updated_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_aca_activity (aca_activity_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_baselines ──
        $createTable('schedule_baselines', "
            CREATE TABLE schedule_baselines (
                bl_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                bl_project_id BIGINT NOT NULL,
                bl_name VARCHAR(200) NOT NULL,
                bl_created_by BIGINT NULL,
                bl_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_bl_project (bl_project_id),
                INDEX idx_bl_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_baseline_activities ──
        $createTable('schedule_baseline_activities', "
            CREATE TABLE schedule_baseline_activities (
                bla_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                bla_baseline_id BIGINT NOT NULL,
                bla_activity_id BIGINT NOT NULL,
                bla_start DATETIME NULL,
                bla_finish DATETIME NULL,
                bla_duration_minutes INT NOT NULL DEFAULT 0,
                INDEX idx_bla_baseline (bla_baseline_id),
                INDEX idx_bla_activity (bla_activity_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_scenarios ──
        $createTable('schedule_scenarios', "
            CREATE TABLE schedule_scenarios (
                scn_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                scn_project_id BIGINT NOT NULL,
                scn_name VARCHAR(200) NOT NULL,
                scn_reason VARCHAR(50) NULL,
                scn_modifications TEXT NULL,
                scn_is_active TINYINT NOT NULL DEFAULT 0,
                scn_createby BIGINT NULL,
                scn_createdate DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_scn_project (scn_project_id),
                INDEX idx_scn_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_drivers ──
        $createTable('schedule_drivers', "
            CREATE TABLE schedule_drivers (
                drv_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                drv_project_id BIGINT NOT NULL,
                drv_type VARCHAR(30) NOT NULL,
                drv_name VARCHAR(500) NOT NULL,
                drv_activity_id BIGINT NULL,
                drv_wbs_id BIGINT NULL,
                drv_constraint_type VARCHAR(10) NULL,
                drv_constraint_date DATETIME NULL,
                drv_window_start DATETIME NULL,
                drv_window_end DATETIME NULL,
                drv_status VARCHAR(20) NOT NULL DEFAULT 'OPEN',
                drv_confidence VARCHAR(10) NOT NULL DEFAULT 'MED',
                drv_evidence_link VARCHAR(500) NULL,
                drv_createby BIGINT NULL,
                drv_createdate DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                drv_modifyby BIGINT NULL,
                drv_modifydate DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_drv_project (drv_project_id),
                INDEX idx_drv_activity (drv_activity_id),
                INDEX idx_drv_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_constraint_log ──
        $createTable('schedule_constraint_log', "
            CREATE TABLE schedule_constraint_log (
                cl_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                cl_project_id BIGINT NOT NULL,
                cl_activity_id BIGINT NOT NULL,
                cl_driver_id BIGINT NULL,
                cl_needed_by_date DATETIME NULL,
                cl_owner_role VARCHAR(50) NULL,
                cl_status VARCHAR(20) NOT NULL DEFAULT 'OPEN',
                cl_notes TEXT NULL,
                cl_createby BIGINT NULL,
                cl_createdate DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                cl_modifyby BIGINT NULL,
                cl_modifydate DATETIME NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_cl_project (cl_project_id),
                INDEX idx_cl_activity (cl_activity_id),
                INDEX idx_cl_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_runs ──
        $createTable('schedule_runs', "
            CREATE TABLE schedule_runs (
                run_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                run_project_id BIGINT NOT NULL,
                run_scenario_id BIGINT NULL,
                run_progress_date DATETIME NULL,
                run_project_finish DATETIME NULL,
                run_total_activities INT NOT NULL DEFAULT 0,
                run_critical_count INT NOT NULL DEFAULT 0,
                run_near_critical_count INT NOT NULL DEFAULT 0,
                run_violations TEXT NULL,
                run_health_summary TEXT NULL,
                run_status VARCHAR(20) NOT NULL DEFAULT 'completed',
                run_computation_ms INT NULL,
                run_created_by BIGINT NULL,
                run_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                company_id BIGINT UNSIGNED NOT NULL,
                INDEX idx_run_project (run_project_id),
                INDEX idx_run_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── schedule_run_activities ──
        $createTable('schedule_run_activities', "
            CREATE TABLE schedule_run_activities (
                ra_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                ra_run_id BIGINT NOT NULL,
                ra_activity_id BIGINT NOT NULL,
                ra_early_start DATETIME NULL,
                ra_early_finish DATETIME NULL,
                ra_late_start DATETIME NULL,
                ra_late_finish DATETIME NULL,
                ra_total_float_minutes INT NULL,
                ra_free_float_minutes INT NULL,
                ra_is_critical TINYINT NOT NULL DEFAULT 0,
                ra_driving_predecessor_id BIGINT NULL,
                ra_driving_constraint_id BIGINT NULL,
                INDEX idx_ra_run (ra_run_id),
                INDEX idx_ra_activity (ra_activity_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ══════════════════════════════════════════
        $messages[] = '';
        $messages[] = '========================================';
        $messages[] = '  ADDING MISSING COLUMNS';
        $messages[] = '========================================';
        $messages[] = '';

        // supplier_master: add sup_type
        $addColumn('supplier_master', 'sup_type', "TINYINT NOT NULL DEFAULT 1 COMMENT '1=Supplier, 2=Subcontractor, 3=Both'");

        // project_master: add scheduling columns
        $addColumn('project_master', 'proj_default_calendar_id', 'BIGINT NULL');
        $addColumn('project_master', 'proj_scheduling_mode', "VARCHAR(20) NULL DEFAULT 'AUTO'");
        $addColumn('project_master', 'proj_progress_date', 'DATETIME NULL');
        $addColumn('project_master', 'proj_target_finish_date', 'DATETIME NULL');

        // purchase_order_details: add backorder columns
        $addColumn('purchase_order_details', 'backordered_qty', 'DECIMAL(18,4) NULL DEFAULT 0');
        $addColumn('purchase_order_details', 'backorder_status', "TINYINT NULL DEFAULT 0");
        $addColumn('purchase_order_details', 'backorder_notes', 'TEXT NULL');
        $addColumn('purchase_order_details', 'expected_backorder_date', 'DATE NULL');

        // approval_requests: add columns the model expects
        $addColumn('approval_requests', 'request_id', 'BIGINT NULL');
        $addColumn('approval_requests', 'request_type', 'VARCHAR(50) NULL');
        $addColumn('approval_requests', 'entity_id', 'BIGINT NULL');
        $addColumn('approval_requests', 'entity_number', 'VARCHAR(50) NULL');
        $addColumn('approval_requests', 'request_amount', 'DECIMAL(18,2) NULL DEFAULT 0');
        $addColumn('approval_requests', 'current_level', 'INT NULL DEFAULT 1');
        $addColumn('approval_requests', 'required_levels', 'INT NULL DEFAULT 1');
        $addColumn('approval_requests', 'request_status', "VARCHAR(20) NULL DEFAULT 'pending'");
        $addColumn('approval_requests', 'current_approver_id', 'BIGINT NULL');
        $addColumn('approval_requests', 'approval_history', 'JSON NULL');
        $addColumn('approval_requests', 'request_notes', 'TEXT NULL');
        $addColumn('approval_requests', 'submitted_at', 'DATETIME NULL');
        $addColumn('approval_requests', 'completed_at', 'DATETIME NULL');
        $addColumn('approval_requests', 'override_by', 'BIGINT NULL');
        $addColumn('approval_requests', 'override_reason', 'TEXT NULL');
        $addColumn('approval_requests', 'override_at', 'DATETIME NULL');
        $addColumn('approval_requests', 'requested_by', 'BIGINT NULL');

        // Backfill request_id = id for existing rows
        try {
            $pdo->exec("UPDATE approval_requests SET request_id = id WHERE request_id IS NULL");
            $messages[] = "[OK]   approval_requests: backfilled request_id = id";
        } catch (\Throwable $e) {
            $messages[] = "[NOTE] approval_requests backfill: " . $e->getMessage();
        }

        // ══════════════════════════════════════════
        $messages[] = '';
        $messages[] = '========================================';
        $messages[] = '  CREATING VIEWS';
        $messages[] = '========================================';
        $messages[] = '';

        // Create vw_budget_summary
        try {
            $pdo->exec("DROP VIEW IF EXISTS vw_budget_summary");
            $pdo->exec("
                CREATE VIEW vw_budget_summary AS
                SELECT
                    b.budget_id,
                    p.proj_id,
                    p.proj_number,
                    p.proj_name,
                    cc.cc_id,
                    cc.cc_no AS cost_code,
                    cc.cc_description AS cost_code_name,
                    b.budget_original_amount,
                    b.budget_revised_amount,
                    b.budget_committed_amount,
                    b.budget_spent_amount,
                    b.budget_remaining_amount,
                    b.budget_fiscal_year,
                    b.company_id,
                    CASE
                        WHEN b.budget_revised_amount > 0
                        THEN CAST((b.budget_committed_amount + b.budget_spent_amount) / b.budget_revised_amount * 100 AS DECIMAL(5,2))
                        ELSE 0
                    END AS budget_utilization_pct
                FROM budget_master b
                INNER JOIN project_master p ON b.budget_project_id = p.proj_id
                INNER JOIN cost_code_master cc ON b.budget_cost_code_id = cc.cc_id
                WHERE b.budget_status = 1
            ");
            $messages[] = "[OK]   vw_budget_summary created";
        } catch (\Throwable $e) {
            $messages[] = "[FAIL] vw_budget_summary: " . $e->getMessage();
        }

        // Create procore_project_mapping table
        $createTable('procore_project_mapping', "
            CREATE TABLE procore_project_mapping (
                ppm_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                ppm_procore_project_id BIGINT NULL,
                ppm_procore_project_name VARCHAR(255) NULL,
                ppm_procore_company_id BIGINT NULL,
                ppm_local_project_id BIGINT NULL,
                ppm_sync_status VARCHAR(20) NOT NULL DEFAULT 'unmapped',
                ppm_last_sync_at DATETIME NULL,
                ppm_auto_sync TINYINT NOT NULL DEFAULT 0,
                ppm_created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                ppm_updated_at DATETIME NULL,
                INDEX idx_ppm_procore (ppm_procore_project_id),
                INDEX idx_ppm_local (ppm_local_project_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Create procore_cost_code_mapping table
        $createTable('procore_cost_code_mapping', "
            CREATE TABLE procore_cost_code_mapping (
                pccm_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                pccm_procore_project_id BIGINT NULL,
                pccm_procore_cost_code_id BIGINT NULL,
                pccm_procore_cost_code_name VARCHAR(255) NULL,
                pccm_local_cost_code_id BIGINT NULL,
                pccm_sync_status VARCHAR(20) NOT NULL DEFAULT 'unmapped',
                pccm_created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                pccm_updated_at DATETIME NULL,
                INDEX idx_pccm_procore (pccm_procore_cost_code_id),
                INDEX idx_pccm_local (pccm_local_cost_code_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Cost Code Templates
        $createTable('cost_code_templates', "
            CREATE TABLE cost_code_templates (
                cct_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT NULL,
                cct_name VARCHAR(150) NOT NULL,
                cct_description VARCHAR(500) NULL,
                cct_status TINYINT NOT NULL DEFAULT 1,
                cct_createby BIGINT NULL,
                cct_createdate DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                cct_modifyby BIGINT NULL,
                cct_modifydate DATETIME NULL,
                INDEX idx_cct_company (company_id),
                INDEX idx_cct_status (cct_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Cost Code Template Items (linking table)
        $createTable('cost_code_template_items', "
            CREATE TABLE cost_code_template_items (
                ccti_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                ccti_template_id BIGINT NOT NULL,
                ccti_cost_code_id BIGINT NOT NULL,
                ccti_sort_order INT NOT NULL DEFAULT 0,
                INDEX idx_ccti_template (ccti_template_id),
                INDEX idx_ccti_costcode (ccti_cost_code_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Project Cost Codes (per-project cost code assignments)
        $createTable('project_cost_codes', "
            CREATE TABLE project_cost_codes (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT NULL,
                project_id BIGINT NOT NULL,
                cost_code_id BIGINT NOT NULL,
                is_active TINYINT NOT NULL DEFAULT 1,
                notes TEXT NULL,
                created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL,
                INDEX idx_pcc_project (project_id),
                INDEX idx_pcc_costcode (cost_code_id),
                INDEX idx_pcc_company (company_id),
                UNIQUE KEY uk_project_costcode (project_id, cost_code_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Create vw_receiving_summary
        try {
            $pdo->exec("DROP VIEW IF EXISTS vw_receiving_summary");
            $pdo->exec("
                CREATE VIEW vw_receiving_summary AS
                SELECT
                    ro.rorder_id,
                    ro.rorder_slip_no,
                    ro.rorder_date,
                    po.porder_id,
                    po.porder_no,
                    p.proj_name,
                    s.sup_name,
                    ro.rorder_totalitem,
                    ro.rorder_totalamount,
                    ro.rorder_status,
                    ro.rorder_createdate,
                    ro.company_id,
                    CASE ro.rorder_status
                        WHEN 0 THEN 'Cancelled'
                        WHEN 1 THEN 'Active'
                        ELSE 'Unknown'
                    END AS status_text
                FROM receive_order_master ro
                INNER JOIN purchase_order_master po ON ro.rorder_porder_ms = po.porder_id
                INNER JOIN project_master p ON po.porder_project_ms = p.proj_id
                INNER JOIN supplier_master s ON po.porder_supplier_ms = s.sup_id
            ");
            $messages[] = "[OK]   vw_receiving_summary created";
        } catch (\Throwable $e) {
            $messages[] = "[FAIL] vw_receiving_summary: " . $e->getMessage();
        }

        // Create vw_back_order_report
        try {
            $pdo->exec("DROP VIEW IF EXISTS vw_back_order_report");
            $pdo->exec("
                CREATE VIEW vw_back_order_report AS
                SELECT
                    pod.po_detail_id,
                    pod.po_detail_item,
                    pod.po_detail_quantity,
                    COALESCE(pod.backordered_qty, 0) AS backordered_qty,
                    pod.expected_backorder_date,
                    COALESCE(pod.backorder_status, 0) AS backorder_status,
                    pom.porder_no,
                    pom.porder_id,
                    pom.porder_project_ms,
                    pom.porder_supplier_ms,
                    proj.proj_name,
                    sup.sup_name,
                    pom.company_id
                FROM purchase_order_details pod
                INNER JOIN purchase_order_master pom ON pom.porder_id = pod.po_detail_porder_ms
                LEFT JOIN project_master proj ON proj.proj_id = pom.porder_project_ms
                LEFT JOIN supplier_master sup ON sup.sup_id = pom.porder_supplier_ms
                WHERE COALESCE(pod.backordered_qty, 0) > 0
            ");
            $messages[] = "[OK]   vw_back_order_report created";
        } catch (\Throwable $e) {
            $messages[] = "[FAIL] vw_back_order_report: " . $e->getMessage();
        }

        // ══════════════════════════════════════════
        $messages[] = '';
        $messages[] = '========================================';
        $messages[] = '  SUMMARY';
        $messages[] = '========================================';
        $messages[] = "Tables created: $created";
        $messages[] = "Tables skipped: $skipped";
        $messages[] = "Errors: $errors";
        $messages[] = '';

        // Count total tables now
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()");
        $total = $stmt->fetchColumn();
        $messages[] = "Total tables in database: $total";

        return response('<pre style="font-family:monospace;padding:20px;background:#f8f9fa;font-size:13px;">' .
            e(implode("\n", $messages)) . '</pre>')
            ->header('Content-Type', 'text/html');
    }

    /**
     * Seed extended data for newly created tables.
     * GET /_dev/seed-extended
     */
    public function seedExtended()
    {
        if (!config('app.debug')) {
            abort(403, 'Only available in debug mode');
        }

        set_time_limit(120);
        $messages = [];

        try {
            $companyId = DB::table('companies')->where('name', 'Demo Construction Co')->value('id');
            if (!$companyId) {
                return response('<pre>Run /_dev/seed first to create base data</pre>', 400);
            }

            // Get existing IDs
            $projectIds = DB::table('project_master')->where('company_id', $companyId)->pluck('proj_id')->toArray();
            $supplierIds = DB::table('supplier_master')->where('company_id', $companyId)->pluck('sup_id')->toArray();
            $costCodeIds = DB::table('cost_code_master')->where('company_id', $companyId)->pluck('cc_id')->toArray();

            // ── Budget records ──
            foreach ($projectIds as $i => $projId) {
                $ccId = $costCodeIds[$i] ?? $costCodeIds[0];
                if (!DB::table('budget_master')->where('budget_project_id', $projId)->where('budget_cost_code_id', $ccId)->exists()) {
                    DB::table('budget_master')->insert([
                        'budget_project_id' => $projId,
                        'budget_cost_code_id' => $ccId,
                        'budget_original_amount' => 500000 + ($i * 250000),
                        'budget_revised_amount' => 500000 + ($i * 250000),
                        'budget_committed_amount' => 0,
                        'budget_spent_amount' => 0,
                        'budget_remaining_amount' => 500000 + ($i * 250000),
                        'budget_fiscal_year' => '2026',
                        'budget_status' => 1,
                        'budget_created_by' => 1,
                        'budget_created_at' => now(),
                        'original_amount' => 500000 + ($i * 250000),
                        'company_id' => $companyId,
                    ]);
                    $messages[] = "[OK] Budget for project $projId";
                } else {
                    $messages[] = "[SKIP] Budget for project $projId already exists";
                }
            }

            // ── Equipment ──
            if (!DB::table('eq_master')->where('eqm_asset_tag', 'CAT-320-001')->exists()) {
                DB::table('eq_master')->insert([
                    'eqm_asset_name' => 'CAT 320 Excavator',
                    'eqm_asset_description' => '20-ton hydraulic excavator',
                    'eqm_asset_type' => 'Heavy Equipment',
                    'eqm_asset_tag' => 'CAT-320-001',
                    'eqm_asset_condition' => 'Good',
                    'eqm_category' => 'Excavators',
                    'eqm_status' => 'Available',
                    'eqm_brand' => 'Caterpillar',
                    'eqm_model' => '320 GC',
                    'eqm_year' => '2024',
                    'eqm_serial' => 'CAT320GC2024001',
                    'eqm_purchase_price' => 185000.00,
                    'eqm_current_value' => 165000.00,
                    'eqm_purchase_date' => '2024-03-15',
                    'eqm_location' => 'Main Yard',
                    'eqm_created_date' => now(),
                    'status' => 1,
                    'company_id' => $companyId,
                ]);
                $messages[] = "[OK] Equipment: CAT 320 Excavator";
            } else {
                $messages[] = "[SKIP] Equipment already exists";
            }

            // ── Permission template ──
            if (!DB::table('permission_master')->where('pt_template_name', 'Full Access')->exists()) {
                DB::table('permission_master')->insert([
                    'pt_template_name' => 'Full Access',
                    'pt_t_porder' => 1, 'pt_t_rorder' => 1, 'pt_t_rcorder' => 1, 'pt_t_rfq' => 1,
                    'pt_m_item' => 1, 'pt_m_uom' => 1, 'pt_m_costcode' => 1, 'pt_m_projects' => 1,
                    'pt_m_suppliers' => 1, 'pt_m_taxgroup' => 1, 'pt_m_budget' => 1, 'pt_m_email' => 1,
                    'pt_i_item' => 1, 'pt_i_itemp' => 1, 'pt_i_supplierc' => 1,
                    'pt_e_eq' => 1, 'pt_e_eqm' => 1, 'pt_e_checklist' => 1,
                    'pt_a_user' => 1, 'pt_a_permissions' => 1, 'pt_a_cinfo' => 1, 'pt_a_procore' => 1,
                    'created_date' => now(), 'status' => 1,
                ]);
                $messages[] = "[OK] Permission template: Full Access";
            } else {
                $messages[] = "[SKIP] Permission template already exists";
            }

            // ── Approval workflow ──
            if (!DB::table('approval_workflows')->where('workflow_name', 'PO Approval')->where('company_id', $companyId)->exists()) {
                DB::table('approval_workflows')->insert([
                    'company_id' => $companyId,
                    'workflow_name' => 'PO Approval',
                    'workflow_type' => 'po',
                    'approval_level' => 1,
                    'amount_threshold_min' => 0,
                    'amount_threshold_max' => 50000,
                    'approver_user_ids' => json_encode([1, 2]),
                    'is_active' => 1,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $messages[] = "[OK] Approval workflow: PO Approval";
            } else {
                $messages[] = "[SKIP] Approval workflow already exists";
            }

            // ── Contract ──
            if (!DB::table('contract_master')->where('contract_number', 'CTR-001')->exists()) {
                DB::table('contract_master')->insert([
                    'contract_number' => 'CTR-001',
                    'contract_title' => 'Foundation Work - Downtown Office Tower',
                    'contract_description' => 'Subcontract for foundation and sitework',
                    'contract_project_id' => $projectIds[0] ?? 1,
                    'contract_supplier_id' => $supplierIds[0] ?? 1,
                    'contract_cost_code_id' => $costCodeIds[1] ?? 1,
                    'contract_original_value' => 750000.00,
                    'contract_status' => 4, // Active
                    'contract_start_date' => '2026-02-01',
                    'contract_end_date' => '2026-08-31',
                    'contract_retention_pct' => 10.00,
                    'contract_created_by' => 1,
                    'contract_created_at' => now(),
                    'company_id' => $companyId,
                ]);
                $messages[] = "[OK] Contract: CTR-001";
            } else {
                $messages[] = "[SKIP] Contract already exists";
            }

            // ── Project role ──
            if (!DB::table('project_roles')->where('project_id', $projectIds[0] ?? 1)->where('user_id', 1)->exists()) {
                DB::table('project_roles')->insert([
                    'company_id' => $companyId,
                    'project_id' => $projectIds[0] ?? 1,
                    'user_id' => 1,
                    'role_name' => 'Admin',
                    'can_create_po' => 1,
                    'can_approve_po' => 1,
                    'can_create_budget_co' => 1,
                    'can_approve_budget_co' => 1,
                    'can_override_budget' => 1,
                    'approval_limit' => 999999.99,
                    'is_active' => 1,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $messages[] = "[OK] Project role: Admin for project " . ($projectIds[0] ?? 1);
            } else {
                $messages[] = "[SKIP] Project role already exists";
            }

            $messages[] = '';
            $messages[] = '=== Extended seeding complete ===';

            return response('<pre style="font-family:monospace;padding:20px;background:#f8f9fa;">' .
                e(implode("\n", $messages)) . '</pre>')
                ->header('Content-Type', 'text/html');
        } catch (\Throwable $e) {
            $messages[] = "ERROR: " . $e->getMessage();
            $messages[] = $e->getFile() . ':' . $e->getLine();
            return response('<pre style="color:red;padding:20px;">' .
                e(implode("\n", $messages)) . '</pre>', 500)
                ->header('Content-Type', 'text/html');
        }
    }

    public function run()
    {
        if (!config('app.debug')) {
            abort(403, 'Only available in debug mode');
        }

        set_time_limit(120);
        $messages = [];

        try {
            $companyId = $this->seedCompany($messages);
            $this->seedUsers($companyId, $messages);
            $uomIds = $this->seedUnitsOfMeasure($messages);
            $categoryIds = $this->seedItemCategories($companyId, $messages);
            $costCodeIds = $this->seedCostCodes($companyId, $messages);
            $taxGroupIds = $this->seedTaxGroups($messages);
            $projectIds = $this->seedProjects($companyId, $messages);
            $supplierIds = $this->seedSuppliers($companyId, $messages);
            $itemCodes = $this->seedItems($companyId, $categoryIds, $costCodeIds, $uomIds, $messages);

            $messages[] = '';
            $messages[] = '=== Seeding Complete ===';
            $messages[] = '';
            $messages[] = 'Login Credentials (password: admin123):';
            $messages[] = '  Super Admin:     superadmin@demo.com';
            $messages[] = '  Company Admin:   admin@demo.com';
            $messages[] = '  Project Manager: manager@demo.com';
            $messages[] = '  Viewer:          viewer@demo.com';
            $messages[] = '  Regular User:    user@demo.com';

            return response('<pre style="font-family:monospace;padding:20px;background:#f8f9fa;">' .
                e(implode("\n", $messages)) . '</pre>')
                ->header('Content-Type', 'text/html');
        } catch (\Throwable $e) {
            $messages[] = "ERROR: " . $e->getMessage();
            $messages[] = $e->getFile() . ':' . $e->getLine();
            return response('<pre style="color:red;padding:20px;">' .
                e(implode("\n", $messages)) . '</pre>', 500)
                ->header('Content-Type', 'text/html');
        }
    }

    public function debugAuth()
    {
        if (!config('app.debug')) {
            abort(403);
        }

        $email = request('email', 'superadmin@demo.com');
        $password = 'admin123';
        $results = [];

        // 1. Check user exists
        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) {
            return response('<pre>User not found: ' . e($email) . '</pre>', 404);
        }

        $results[] = "User found: id={$user->id}, email={$user->email}, u_type={$user->u_type}";
        $results[] = "Password hash: {$user->password}";
        $results[] = "Hash length: " . strlen($user->password);
        $results[] = "Hash starts with \$2y\$: " . (str_starts_with($user->password, '$2y$') ? 'YES' : 'NO');
        $results[] = "";

        // 2. Test Hash::check directly
        $hashCheck = Hash::check($password, $user->password);
        $results[] = "Hash::check('admin123', stored_hash): " . ($hashCheck ? 'TRUE (MATCH)' : 'FALSE (NO MATCH)');

        // 3. Test password_verify directly
        $phpCheck = password_verify($password, $user->password);
        $results[] = "password_verify('admin123', stored_hash): " . ($phpCheck ? 'TRUE' : 'FALSE');
        $results[] = "";

        // 4. Generate a new hash and verify it
        $newHash = Hash::make($password);
        $results[] = "Fresh Hash::make('admin123'): {$newHash}";
        $results[] = "Hash::check against fresh hash: " . (Hash::check($password, $newHash) ? 'TRUE' : 'FALSE');
        $results[] = "";

        // 5. Try Auth::attempt
        $results[] = "PHP version: " . phpversion();
        $results[] = "PASSWORD_BCRYPT constant: " . PASSWORD_BCRYPT;
        $results[] = "";

        try {
            $authResult = \Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password]);
            $results[] = "Auth::attempt result: " . ($authResult ? 'TRUE (SUCCESS)' : 'FALSE (FAILED)');
            if ($authResult) {
                $authUser = \Illuminate\Support\Facades\Auth::user();
                $results[] = "Auth::user(): id={$authUser->id}, email={$authUser->email}";
                \Illuminate\Support\Facades\Auth::logout();
            }
        } catch (\Throwable $e) {
            $results[] = "Auth::attempt EXCEPTION: " . $e->getMessage();
            $results[] = "  at " . $e->getFile() . ':' . $e->getLine();
        }

        // 6. Fix: update password if it doesn't verify
        if (!$hashCheck) {
            $results[] = "";
            $results[] = "--- AUTO-FIX: Updating password hash ---";
            DB::table('users')->where('id', $user->id)->update(['password' => $newHash]);
            $results[] = "Password updated to fresh hash.";

            // Re-test
            $updatedUser = DB::table('users')->where('id', $user->id)->first();
            $reCheck = Hash::check($password, $updatedUser->password);
            $results[] = "Re-check after update: " . ($reCheck ? 'TRUE (FIXED)' : 'FALSE (STILL BROKEN)');
        }

        return response('<pre style="padding:20px;font-family:monospace;">' .
            e(implode("\n", $results)) . '</pre>')
            ->header('Content-Type', 'text/html');
    }

    private function seedCompany(array &$msg): int
    {
        $existing = DB::table('companies')->where('name', 'Demo Construction Co')->first();
        if ($existing) {
            $msg[] = '[SKIP] Company "Demo Construction Co" already exists (id=' . $existing->id . ')';
            return $existing->id;
        }
        $id = DB::table('companies')->insertGetId([
            'name' => 'Demo Construction Co', 'subdomain' => 'demo', 'status' => 1,
            'settings' => json_encode(['currency' => 'USD', 'timezone' => 'America/New_York']),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $msg[] = "[OK] Created company (id=$id)";
        return $id;
    }

    private function seedUsers(int $companyId, array &$msg): void
    {
        $users = [
            ['name' => 'Super Admin',    'email' => 'superadmin@demo.com', 'username' => 'superadmin',   'u_type' => 1],
            ['name' => 'Company Admin',  'email' => 'admin@demo.com',      'username' => 'companyadmin', 'u_type' => 2],
            ['name' => 'Project Manager','email' => 'manager@demo.com',    'username' => 'manager',      'u_type' => 3],
            ['name' => 'Viewer User',    'email' => 'viewer@demo.com',     'username' => 'viewer',       'u_type' => 4],
            ['name' => 'Regular User',   'email' => 'user@demo.com',       'username' => 'user',         'u_type' => 0],
        ];
        foreach ($users as $u) {
            if (DB::table('users')->where('email', $u['email'])->exists()) {
                // Always reset password to ensure it's valid
                DB::table('users')->where('email', $u['email'])->update([
                    'password' => Hash::make('admin123'),
                    'company_id' => $companyId,
                    'u_type' => $u['u_type'],
                    'u_status' => 1,
                ]);
                $msg[] = "[OK] User {$u['email']} - password reset";
                continue;
            }
            DB::table('users')->insert([
                'name' => $u['name'], 'email' => $u['email'], 'username' => $u['username'],
                'password' => Hash::make('admin123'), 'company_id' => $companyId,
                'u_type' => $u['u_type'], 'u_status' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $msg[] = "[OK] User {$u['email']} (u_type={$u['u_type']})";
        }
    }

    private function seedUnitsOfMeasure(array &$msg): array
    {
        $uoms = ['Each', 'Box', 'Cubic Yard', 'Linear Foot', 'Square Foot', 'Ton', 'Gallon', 'Bag'];
        $ids = [];
        foreach ($uoms as $name) {
            $existing = DB::table('unit_of_measure_tab')->where('uom_name', $name)->first();
            if ($existing) { $ids[] = $existing->uom_id; continue; }
            $ids[] = DB::table('unit_of_measure_tab')->insertGetId([
                'uom_name' => $name, 'uom_detail' => $name,
                'uom_createdate' => now(), 'uom_createby' => 1, 'uom_status' => 1,
            ], 'uom_id');
        }
        $msg[] = "[OK] UOM: " . count($ids);
        return $ids;
    }

    private function seedItemCategories(int $companyId, array &$msg): array
    {
        $categories = ['Concrete & Masonry', 'Structural Steel', 'Lumber & Wood', 'Electrical',
                        'Plumbing', 'HVAC', 'Safety Equipment', 'Rental Equipment'];
        $ids = [];
        foreach ($categories as $name) {
            $existing = DB::table('item_category_tab')->where('icat_name', $name)->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->icat_id; continue; }
            $ids[] = DB::table('item_category_tab')->insertGetId([
                'icat_name' => $name, 'icat_details' => "$name materials",
                'icat_status' => 1, 'icat_createby' => 1, 'icat_createdate' => now(),
                'company_id' => $companyId,
            ], 'icat_id');
        }
        $msg[] = "[OK] Categories: " . count($ids);
        return $ids;
    }

    private function seedCostCodes(int $companyId, array &$msg): array
    {
        $codes = [
            ['cc_no' => '01', 'cc_description' => 'General Requirements', 'cc_full_code' => '01', 'cc_level' => 1, 'cc_parent_code' => '01'],
            ['cc_no' => '03', 'cc_description' => 'Concrete',             'cc_full_code' => '03', 'cc_level' => 1, 'cc_parent_code' => '03'],
            ['cc_no' => '05', 'cc_description' => 'Metals',               'cc_full_code' => '05', 'cc_level' => 1, 'cc_parent_code' => '05'],
            ['cc_no' => '06', 'cc_description' => 'Wood/Plastics',        'cc_full_code' => '06', 'cc_level' => 1, 'cc_parent_code' => '06'],
            ['cc_no' => '26', 'cc_description' => 'Electrical',           'cc_full_code' => '26', 'cc_level' => 1, 'cc_parent_code' => '26'],
            ['cc_no' => '03-10', 'cc_description' => 'Cast-in-Place Concrete', 'cc_full_code' => '03-10', 'cc_level' => 2, 'cc_parent_code' => '03'],
            ['cc_no' => '03-20', 'cc_description' => 'Reinforcing Steel',      'cc_full_code' => '03-20', 'cc_level' => 2, 'cc_parent_code' => '03'],
            ['cc_no' => '05-10', 'cc_description' => 'Structural Steel',       'cc_full_code' => '05-10', 'cc_level' => 2, 'cc_parent_code' => '05'],
            ['cc_no' => '26-10', 'cc_description' => 'Medium-Voltage Elec',    'cc_full_code' => '26-10', 'cc_level' => 2, 'cc_parent_code' => '26'],
        ];
        $ids = [];
        foreach ($codes as $cc) {
            $existing = DB::table('cost_code_master')->where('cc_no', $cc['cc_no'])->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->cc_id; continue; }
            $ids[] = DB::table('cost_code_master')->insertGetId(array_merge($cc, [
                'cc_status' => 1, 'cc_createby' => 1, 'cc_createdate' => now(), 'company_id' => $companyId,
            ]), 'cc_id');
        }
        $msg[] = "[OK] Cost codes: " . count($ids);
        return $ids;
    }

    private function seedTaxGroups(array &$msg): array
    {
        $groups = [
            ['name' => 'No Tax', 'percentage' => 0.00],
            ['name' => 'State Tax', 'percentage' => 6.25],
            ['name' => 'Full Tax', 'percentage' => 8.875],
        ];
        $ids = [];
        foreach ($groups as $g) {
            $existing = DB::table('taxgroup_master')->where('name', $g['name'])->first();
            if ($existing) { $ids[] = $existing->id; continue; }
            $ids[] = DB::table('taxgroup_master')->insertGetId(array_merge($g, ['created_at' => now()]));
        }
        $msg[] = "[OK] Tax groups: " . count($ids);
        return $ids;
    }

    private function seedProjects(int $companyId, array &$msg): array
    {
        $projects = [
            ['proj_name' => 'Downtown Office Tower',     'proj_number' => 'DOT-001'],
            ['proj_name' => 'Harbor Bridge Renovation',  'proj_number' => 'HBR-002'],
            ['proj_name' => 'Riverside School Expansion', 'proj_number' => 'RSE-003'],
        ];
        $ids = [];
        foreach ($projects as $p) {
            $existing = DB::table('project_master')->where('proj_number', $p['proj_number'])->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->proj_id; continue; }
            $ids[] = DB::table('project_master')->insertGetId(array_merge($p, [
                'proj_status' => 1, 'proj_createby' => 1, 'proj_createdate' => now(),
                'proj_address' => 'Demo Address', 'proj_contact' => 0,
                'proj_start_date' => '2026-01-15', 'proj_end_date' => '2027-06-30',
                'company_id' => $companyId,
            ]), 'proj_id');
        }
        $msg[] = "[OK] Projects: " . count($ids);
        return $ids;
    }

    private function seedSuppliers(int $companyId, array &$msg): array
    {
        $suppliers = [
            ['sup_name' => 'Apex Steel Industries', 'sup_code' => 'APEX-001', 'sup_email' => 'orders@apexsteel.com'],
            ['sup_name' => 'ReadyMix Concrete Co',  'sup_code' => 'RMC-002',  'sup_email' => 'dispatch@readymix.com'],
            ['sup_name' => 'National Lumber Supply', 'sup_code' => 'NLS-003',  'sup_email' => 'sales@nationallumber.com'],
            ['sup_name' => 'ProElectric Wholesale',  'sup_code' => 'PEW-004',  'sup_email' => 'info@proelectric.com'],
        ];
        $ids = [];
        foreach ($suppliers as $s) {
            $existing = DB::table('supplier_master')->where('sup_code', $s['sup_code'])->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->sup_id; continue; }
            $ids[] = DB::table('supplier_master')->insertGetId(array_merge($s, [
                'sup_status' => 1, 'sup_createby' => 1, 'sup_createdate' => now(),
                'sup_address' => 'Demo Address', 'sup_contact_person' => 'Demo Contact', 'sup_phone' => '555-000-0000',
                'company_id' => $companyId,
            ]), 'sup_id');
        }
        $msg[] = "[OK] Suppliers: " . count($ids);
        return $ids;
    }

    private function seedItems(int $companyId, array $catIds, array $ccIds, array $uomIds, array &$msg): array
    {
        $items = [
            ['item_code' => 'CONC-001', 'item_name' => 'Ready Mix Concrete 4000 PSI', 'item_cat_ms' => $catIds[0] ?? 1, 'item_ccode_ms' => $ccIds[5] ?? 1, 'item_unit_ms' => $uomIds[2] ?? 1],
            ['item_code' => 'STL-001',  'item_name' => 'W12x26 Structural Beam',      'item_cat_ms' => $catIds[1] ?? 1, 'item_ccode_ms' => $ccIds[7] ?? 1, 'item_unit_ms' => $uomIds[3] ?? 1],
            ['item_code' => 'LBR-001',  'item_name' => '2x4x8 SPF Stud',              'item_cat_ms' => $catIds[2] ?? 1, 'item_ccode_ms' => $ccIds[3] ?? 1, 'item_unit_ms' => $uomIds[0] ?? 1],
            ['item_code' => 'ELEC-001', 'item_name' => '12/2 Romex Wire 250ft',       'item_cat_ms' => $catIds[3] ?? 1, 'item_ccode_ms' => $ccIds[8] ?? 1, 'item_unit_ms' => $uomIds[1] ?? 1],
            ['item_code' => 'SAFE-001', 'item_name' => 'Hard Hat - White',             'item_cat_ms' => $catIds[6] ?? 1, 'item_ccode_ms' => $ccIds[0] ?? 1, 'item_unit_ms' => $uomIds[0] ?? 1],
        ];
        $codes = [];
        foreach ($items as $item) {
            $existing = DB::table('item_master')->where('item_code', $item['item_code'])->where('company_id', $companyId)->first();
            if ($existing) { $codes[] = $item['item_code']; continue; }
            DB::table('item_master')->insert(array_merge($item, [
                'item_description' => $item['item_name'],
                'item_status' => 1, 'item_createby' => 1, 'item_createdate' => now(),
                'company_id' => $companyId,
            ]));
            $codes[] = $item['item_code'];
        }
        $msg[] = "[OK] Items: " . count($codes);
        return $codes;
    }
}
