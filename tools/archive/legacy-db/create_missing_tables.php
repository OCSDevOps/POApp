<?php
/**
 * Create Missing Database Tables for POApp
 *
 * Tables created:
 *   1. checklist_master       (Checklist model)
 *   2. checklist_details      (ChecklistItem model - child of checklist_master)
 *   3. cl_perform_master      (ChecklistPerformance model)
 *   4. cl_perform_details     (ChecklistPerformanceDetail model - child of cl_perform_master)
 *   5. eq_master              (Equipment model)
 *   6. permission_master      (PermissionTemplate model)
 *   7. procore_auth           (ProcoreService)
 *
 * Connection: sqlsrv / DESKTOP-Q2001NS\SQLEXPRESS / porder_db (Windows Auth)
 */

$serverName = 'DESKTOP-Q2001NS\SQLEXPRESS';
$database   = 'porder_db';

try {
    // Connect using Windows Authentication (no username/password in .env)
    $dsn = "sqlsrv:Server={$serverName};Database={$database};TrustServerCertificate=true";
    $pdo = new PDO($dsn, '', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "Connected to {$database} on {$serverName} successfully.\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// ============================================================
// Helper: run SQL only if the table does not already exist
// ============================================================
function createTableIfNotExists(PDO $pdo, string $tableName, string $sql): void
{
    $check = $pdo->query(
        "SELECT CASE WHEN OBJECT_ID('{$tableName}', 'U') IS NOT NULL THEN 1 ELSE 0 END AS table_exists"
    )->fetch(PDO::FETCH_ASSOC);

    if ($check['table_exists'] == 1) {
        echo "[SKIP]  Table '{$tableName}' already exists.\n";
        return;
    }

    $pdo->exec($sql);
    echo "[OK]    Table '{$tableName}' created successfully.\n";
}

// ============================================================
// 1. checklist_master
//    Model: Checklist  (uses CompanyScope)
//    PK: cl_id (identity)
//    Fillable: cl_name, cl_frequency, cl_eq_ids (array/json),
//              cl_user_ids (array/json), cl_start_date, status,
//              created_date, modified_date, company_id
// ============================================================
createTableIfNotExists($pdo, 'checklist_master', "
    CREATE TABLE checklist_master (
        cl_id               INT IDENTITY(1,1) PRIMARY KEY,
        cl_name             NVARCHAR(255)   NOT NULL,
        cl_frequency        NVARCHAR(100)   NULL,
        cl_eq_ids           NVARCHAR(MAX)   NULL,       -- JSON array of equipment IDs
        cl_user_ids         NVARCHAR(MAX)   NULL,       -- JSON array of user IDs
        cl_start_date       DATE            NULL,
        status              INT             NOT NULL DEFAULT 1,
        created_date        DATETIME        NULL,
        modified_date       DATETIME        NULL,
        company_id          INT             NULL
    )
");

// ============================================================
// 2. checklist_details  (child table of checklist_master)
//    Model: ChecklistItem
//    PK: cli_id (identity)
//    Fillable: cl_id, cli_item, status, created_date, modified_date
// ============================================================
createTableIfNotExists($pdo, 'checklist_details', "
    CREATE TABLE checklist_details (
        cli_id              INT IDENTITY(1,1) PRIMARY KEY,
        cl_id               INT             NOT NULL,
        cli_item            NVARCHAR(255)   NOT NULL,
        status              INT             NOT NULL DEFAULT 1,
        created_date        DATETIME        NULL,
        modified_date       DATETIME        NULL
    )
");

// ============================================================
// 3. cl_perform_master
//    Model: ChecklistPerformance
//    PK: cl_p_id (identity)
//    Fillable: cl_id, cl_eq_id, cl_p_date, cl_p_item_values (array/json),
//              status, created_date, modified_date
// ============================================================
createTableIfNotExists($pdo, 'cl_perform_master', "
    CREATE TABLE cl_perform_master (
        cl_p_id             INT IDENTITY(1,1) PRIMARY KEY,
        cl_id               INT             NOT NULL,
        cl_eq_id            INT             NULL,
        cl_p_date           DATE            NULL,
        cl_p_item_values    NVARCHAR(MAX)   NULL,       -- JSON array of item values
        status              INT             NOT NULL DEFAULT 1,
        created_date        DATETIME        NULL,
        modified_date       DATETIME        NULL
    )
");

// ============================================================
// 4. cl_perform_details  (child table of cl_perform_master)
//    Model: ChecklistPerformanceDetail
//    PK: cl_pd_id (identity)
//    Fillable: cl_p_id, cl_pd_cli_id, cl_pd_cli_value,
//              cl_pd_cli_notes, cl_pd_cli_attachment,
//              status, created_date, modified_date
// ============================================================
createTableIfNotExists($pdo, 'cl_perform_details', "
    CREATE TABLE cl_perform_details (
        cl_pd_id            INT IDENTITY(1,1) PRIMARY KEY,
        cl_p_id             INT             NOT NULL,
        cl_pd_cli_id        INT             NOT NULL,
        cl_pd_cli_value     NVARCHAR(255)   NULL,
        cl_pd_cli_notes     NVARCHAR(MAX)   NULL,
        cl_pd_cli_attachment NVARCHAR(500)  NULL,
        status              INT             NOT NULL DEFAULT 1,
        created_date        DATETIME        NULL,
        modified_date       DATETIME        NULL
    )
");

// ============================================================
// 5. eq_master
//    Model: Equipment  (uses CompanyScope)
//    PK: eq_id (identity)
//    Fillable: eqm_asset_name, eqm_asset_description, eqm_asset_type,
//              eqm_asset_tag, eqm_asset_picture, eqm_asset_condition,
//              eqm_category, eqm_status, eqm_existing_reading,
//              eqm_estimate_usage, eqm_remaining_life, eqm_location,
//              eqm_supplier, eqm_serial, eqm_year, eqm_license_plate,
//              eqm_current_operator, eqm_purchase_price, eqm_purchase_date,
//              eqm_current_value, eqm_brand, eqm_model, eqm_war_expiry_date,
//              eqm_dep_method, eqm_rental_total_value, eqm_rental_insurance,
//              eqm_rental_insurance_amt, eqm_created_date, company_id
//    Also has scopeActive -> uses 'status' column
// ============================================================
createTableIfNotExists($pdo, 'eq_master', "
    CREATE TABLE eq_master (
        eq_id                   INT IDENTITY(1,1) PRIMARY KEY,
        eqm_asset_name          NVARCHAR(191)   NOT NULL,
        eqm_asset_description   NVARCHAR(1000)  NULL,
        eqm_asset_type          NVARCHAR(100)   NULL,
        eqm_asset_tag           NVARCHAR(191)   NULL,
        eqm_asset_picture       NVARCHAR(500)   NULL,
        eqm_asset_condition     NVARCHAR(100)   NULL,
        eqm_category            NVARCHAR(191)   NULL,
        eqm_status              NVARCHAR(50)    NULL,
        eqm_existing_reading    DECIMAL(18,2)   NULL,
        eqm_estimate_usage      DECIMAL(18,2)   NULL,
        eqm_remaining_life      DECIMAL(18,2)   NULL,
        eqm_location            NVARCHAR(191)   NULL,
        eqm_supplier            INT             NULL,
        eqm_serial              NVARCHAR(191)   NULL,
        eqm_year                NVARCHAR(50)    NULL,
        eqm_license_plate       NVARCHAR(191)   NULL,
        eqm_current_operator    INT             NULL,
        eqm_purchase_price      DECIMAL(18,2)   NULL,
        eqm_purchase_date       DATE            NULL,
        eqm_current_value       DECIMAL(18,2)   NULL,
        eqm_brand               NVARCHAR(191)   NULL,
        eqm_model               NVARCHAR(191)   NULL,
        eqm_war_expiry_date     DATE            NULL,
        eqm_dep_method          NVARCHAR(100)   NULL,
        eqm_rental_total_value  DECIMAL(18,2)   NULL,
        eqm_rental_insurance    NVARCHAR(191)   NULL,
        eqm_rental_insurance_amt DECIMAL(18,2)  NULL,
        eqm_created_date        DATETIME        NULL,
        status                  INT             NOT NULL DEFAULT 1,
        company_id              INT             NULL
    )
");

// ============================================================
// 6. permission_master
//    Model: PermissionTemplate  (NO CompanyScope)
//    PK: pt_id (identity)
//    Fillable: pt_template_name, pt_template_users (json),
//              pt_t_porder..pt_a_procore (all integer permission flags),
//              created_date, status
// ============================================================
createTableIfNotExists($pdo, 'permission_master', "
    CREATE TABLE permission_master (
        pt_id               INT IDENTITY(1,1) PRIMARY KEY,
        pt_template_name    NVARCHAR(191)   NOT NULL,
        pt_template_users   NVARCHAR(MAX)   NULL,       -- JSON array of user IDs
        pt_t_porder         INT             NOT NULL DEFAULT 0,
        pt_t_rorder         INT             NOT NULL DEFAULT 0,
        pt_t_rcorder        INT             NOT NULL DEFAULT 0,
        pt_t_rfq            INT             NOT NULL DEFAULT 0,
        pt_m_item           INT             NOT NULL DEFAULT 0,
        pt_m_uom            INT             NOT NULL DEFAULT 0,
        pt_m_costcode       INT             NOT NULL DEFAULT 0,
        pt_m_projects       INT             NOT NULL DEFAULT 0,
        pt_m_suppliers      INT             NOT NULL DEFAULT 0,
        pt_m_taxgroup       INT             NOT NULL DEFAULT 0,
        pt_m_budget         INT             NOT NULL DEFAULT 0,
        pt_m_email          INT             NOT NULL DEFAULT 0,
        pt_i_item           INT             NOT NULL DEFAULT 0,
        pt_i_itemp          INT             NOT NULL DEFAULT 0,
        pt_i_supplierc      INT             NOT NULL DEFAULT 0,
        pt_e_eq             INT             NOT NULL DEFAULT 0,
        pt_e_eqm            INT             NOT NULL DEFAULT 0,
        pt_e_checklist      INT             NOT NULL DEFAULT 0,
        pt_a_user           INT             NOT NULL DEFAULT 0,
        pt_a_permissions    INT             NOT NULL DEFAULT 0,
        pt_a_cinfo          INT             NOT NULL DEFAULT 0,
        pt_a_procore        INT             NOT NULL DEFAULT 0,
        created_date        DATETIME        NULL,
        status              INT             NOT NULL DEFAULT 1
    )
");

// ============================================================
// 7. procore_auth
//    Used by: ProcoreService->loadProcoreAuth()
//    Columns accessed: CLIENT_ID, SECRET_KEY, COMPANY_ID
//    Simple single-row config table
// ============================================================
createTableIfNotExists($pdo, 'procore_auth', "
    CREATE TABLE procore_auth (
        id                  INT IDENTITY(1,1) PRIMARY KEY,
        CLIENT_ID           NVARCHAR(255)   NULL,
        SECRET_KEY          NVARCHAR(255)   NULL,
        COMPANY_ID          NVARCHAR(255)   NULL,
        created_at          DATETIME        NULL,
        updated_at          DATETIME        NULL
    )
");

echo "\n--------------------------------------------------\n";
echo "All table creation statements executed.\n";
echo "--------------------------------------------------\n";

// Close connection
$pdo = null;
