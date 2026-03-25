<?php
/**
 * Create scheduling engine tables for POApp.
 * Uses direct PDO to avoid Laravel OOM issues.
 *
 * Tables: 13 new tables + 4 columns added to project_master
 */
ini_set('memory_limit', '512M');

$serverName = 'DESKTOP-Q2001NS\SQLEXPRESS';
$database = 'porder_db';

try {
    $pdo = new PDO(
        "sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=yes",
        null,
        null,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to database.\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$tables = [];

// ─────────────────────────────────────────────────────────────
// 1. schedule_calendars
// ─────────────────────────────────────────────────────────────
$tables['schedule_calendars'] = "
CREATE TABLE schedule_calendars (
    cal_id          BIGINT IDENTITY(1,1) PRIMARY KEY,
    cal_project_id  BIGINT NULL,
    cal_name        VARCHAR(200) NOT NULL,
    cal_timezone    VARCHAR(100) NOT NULL DEFAULT 'America/New_York',
    cal_work_week   VARCHAR(50) NOT NULL DEFAULT 'Mon,Tue,Wed,Thu,Fri',
    cal_work_start  VARCHAR(10) NOT NULL DEFAULT '07:00',
    cal_work_end    VARCHAR(10) NOT NULL DEFAULT '15:30',
    cal_is_default  TINYINT NOT NULL DEFAULT 0,
    cal_status      TINYINT NOT NULL DEFAULT 1,
    cal_createby    BIGINT NULL,
    cal_createdate  DATETIME NULL DEFAULT GETDATE(),
    cal_modifyby    BIGINT NULL,
    cal_modifydate  DATETIME NULL,
    company_id      BIGINT NOT NULL,
    CONSTRAINT fk_cal_project FOREIGN KEY (cal_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_cal_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 2. schedule_calendar_exceptions
// ─────────────────────────────────────────────────────────────
$tables['schedule_calendar_exceptions'] = "
CREATE TABLE schedule_calendar_exceptions (
    cex_id          BIGINT IDENTITY(1,1) PRIMARY KEY,
    cex_calendar_id BIGINT NOT NULL,
    cex_date        DATE NOT NULL,
    cex_type        VARCHAR(20) NOT NULL DEFAULT 'holiday',
    cex_name        VARCHAR(200) NULL,
    cex_work_start  VARCHAR(10) NULL,
    cex_work_end    VARCHAR(10) NULL,
    CONSTRAINT fk_cex_calendar FOREIGN KEY (cex_calendar_id) REFERENCES schedule_calendars(cal_id) ON DELETE CASCADE
)";

// ─────────────────────────────────────────────────────────────
// 3. schedule_wbs_nodes
// ─────────────────────────────────────────────────────────────
$tables['schedule_wbs_nodes'] = "
CREATE TABLE schedule_wbs_nodes (
    wbs_id          BIGINT IDENTITY(1,1) PRIMARY KEY,
    wbs_project_id  BIGINT NOT NULL,
    wbs_parent_id   BIGINT NULL,
    wbs_code        VARCHAR(50) NOT NULL,
    wbs_name        VARCHAR(250) NOT NULL,
    wbs_sort_order  INT NOT NULL DEFAULT 0,
    wbs_status      TINYINT NOT NULL DEFAULT 1,
    company_id      BIGINT NOT NULL,
    CONSTRAINT fk_wbs_project FOREIGN KEY (wbs_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_wbs_parent FOREIGN KEY (wbs_parent_id) REFERENCES schedule_wbs_nodes(wbs_id),
    CONSTRAINT fk_wbs_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 4. schedule_activities
// ─────────────────────────────────────────────────────────────
$tables['schedule_activities'] = "
CREATE TABLE schedule_activities (
    act_id                      BIGINT IDENTITY(1,1) PRIMARY KEY,
    act_project_id              BIGINT NOT NULL,
    act_wbs_id                  BIGINT NULL,
    act_name                    VARCHAR(500) NOT NULL,
    act_description             TEXT NULL,
    act_type                    VARCHAR(20) NOT NULL DEFAULT 'TASK',
    act_duration_minutes        INT NOT NULL DEFAULT 0,
    act_calendar_id             BIGINT NULL,
    act_status                  VARCHAR(20) NOT NULL DEFAULT 'NOT_STARTED',
    act_percent_complete        DECIMAL(5,2) NOT NULL DEFAULT 0,
    act_is_locked               TINYINT NOT NULL DEFAULT 0,
    act_priority                INT NOT NULL DEFAULT 500,
    act_constraint_type         VARCHAR(10) NOT NULL DEFAULT 'NONE',
    act_constraint_date         DATETIME NULL,
    act_early_start             DATETIME NULL,
    act_early_finish            DATETIME NULL,
    act_late_start              DATETIME NULL,
    act_late_finish             DATETIME NULL,
    act_total_float_minutes     INT NULL,
    act_free_float_minutes      INT NULL,
    act_is_critical             TINYINT NOT NULL DEFAULT 0,
    act_driving_predecessor_id  BIGINT NULL,
    act_driving_constraint_id   BIGINT NULL,
    act_sort_order              INT NOT NULL DEFAULT 0,
    act_color                   VARCHAR(20) NULL,
    act_createby                BIGINT NULL,
    act_createdate              DATETIME NULL DEFAULT GETDATE(),
    act_modifyby                BIGINT NULL,
    act_modifydate              DATETIME NULL,
    company_id                  BIGINT NOT NULL,
    CONSTRAINT fk_act_project FOREIGN KEY (act_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_act_wbs FOREIGN KEY (act_wbs_id) REFERENCES schedule_wbs_nodes(wbs_id),
    CONSTRAINT fk_act_calendar FOREIGN KEY (act_calendar_id) REFERENCES schedule_calendars(cal_id),
    CONSTRAINT fk_act_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 5. schedule_dependencies
// ─────────────────────────────────────────────────────────────
$tables['schedule_dependencies'] = "
CREATE TABLE schedule_dependencies (
    dep_id                  BIGINT IDENTITY(1,1) PRIMARY KEY,
    dep_project_id          BIGINT NOT NULL,
    dep_predecessor_id      BIGINT NOT NULL,
    dep_successor_id        BIGINT NOT NULL,
    dep_type                VARCHAR(2) NOT NULL DEFAULT 'FS',
    dep_lag_minutes         INT NOT NULL DEFAULT 0,
    dep_lag_calendar_mode   VARCHAR(20) NOT NULL DEFAULT 'SUCCESSOR',
    company_id              BIGINT NOT NULL,
    CONSTRAINT fk_dep_project FOREIGN KEY (dep_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_dep_pred FOREIGN KEY (dep_predecessor_id) REFERENCES schedule_activities(act_id),
    CONSTRAINT fk_dep_succ FOREIGN KEY (dep_successor_id) REFERENCES schedule_activities(act_id),
    CONSTRAINT fk_dep_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 6. schedule_activity_actuals
// ─────────────────────────────────────────────────────────────
$tables['schedule_activity_actuals'] = "
CREATE TABLE schedule_activity_actuals (
    aca_id                          BIGINT IDENTITY(1,1) PRIMARY KEY,
    aca_activity_id                 BIGINT NOT NULL,
    aca_actual_start                DATETIME NULL,
    aca_actual_finish               DATETIME NULL,
    aca_remaining_duration_minutes  INT NULL,
    aca_note                        TEXT NULL,
    aca_updated_by                  BIGINT NULL,
    aca_updated_at                  DATETIME NULL DEFAULT GETDATE(),
    CONSTRAINT fk_aca_activity FOREIGN KEY (aca_activity_id) REFERENCES schedule_activities(act_id) ON DELETE CASCADE
)";

// ─────────────────────────────────────────────────────────────
// 7. schedule_baselines
// ─────────────────────────────────────────────────────────────
$tables['schedule_baselines'] = "
CREATE TABLE schedule_baselines (
    bl_id           BIGINT IDENTITY(1,1) PRIMARY KEY,
    bl_project_id   BIGINT NOT NULL,
    bl_name         VARCHAR(200) NOT NULL,
    bl_created_by   BIGINT NULL,
    bl_created_at   DATETIME NOT NULL DEFAULT GETDATE(),
    company_id      BIGINT NOT NULL,
    CONSTRAINT fk_bl_project FOREIGN KEY (bl_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_bl_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 8. schedule_baseline_activities
// ─────────────────────────────────────────────────────────────
$tables['schedule_baseline_activities'] = "
CREATE TABLE schedule_baseline_activities (
    bla_id              BIGINT IDENTITY(1,1) PRIMARY KEY,
    bla_baseline_id     BIGINT NOT NULL,
    bla_activity_id     BIGINT NOT NULL,
    bla_start           DATETIME NULL,
    bla_finish          DATETIME NULL,
    bla_duration_minutes INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_bla_baseline FOREIGN KEY (bla_baseline_id) REFERENCES schedule_baselines(bl_id) ON DELETE CASCADE,
    CONSTRAINT fk_bla_activity FOREIGN KEY (bla_activity_id) REFERENCES schedule_activities(act_id)
)";

// ─────────────────────────────────────────────────────────────
// 9. schedule_scenarios
// ─────────────────────────────────────────────────────────────
$tables['schedule_scenarios'] = "
CREATE TABLE schedule_scenarios (
    scn_id          BIGINT IDENTITY(1,1) PRIMARY KEY,
    scn_project_id  BIGINT NOT NULL,
    scn_name        VARCHAR(200) NOT NULL,
    scn_reason      VARCHAR(50) NULL,
    scn_modifications TEXT NULL,
    scn_is_active   TINYINT NOT NULL DEFAULT 0,
    scn_createby    BIGINT NULL,
    scn_createdate  DATETIME NULL DEFAULT GETDATE(),
    company_id      BIGINT NOT NULL,
    CONSTRAINT fk_scn_project FOREIGN KEY (scn_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_scn_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 10. schedule_drivers
// ─────────────────────────────────────────────────────────────
$tables['schedule_drivers'] = "
CREATE TABLE schedule_drivers (
    drv_id              BIGINT IDENTITY(1,1) PRIMARY KEY,
    drv_project_id      BIGINT NOT NULL,
    drv_type            VARCHAR(30) NOT NULL,
    drv_name            VARCHAR(500) NOT NULL,
    drv_activity_id     BIGINT NULL,
    drv_wbs_id          BIGINT NULL,
    drv_constraint_type VARCHAR(10) NULL,
    drv_constraint_date DATETIME NULL,
    drv_window_start    DATETIME NULL,
    drv_window_end      DATETIME NULL,
    drv_status          VARCHAR(20) NOT NULL DEFAULT 'OPEN',
    drv_confidence      VARCHAR(10) NOT NULL DEFAULT 'MED',
    drv_evidence_link   VARCHAR(500) NULL,
    drv_createby        BIGINT NULL,
    drv_createdate      DATETIME NULL DEFAULT GETDATE(),
    drv_modifyby        BIGINT NULL,
    drv_modifydate      DATETIME NULL,
    company_id          BIGINT NOT NULL,
    CONSTRAINT fk_drv_project FOREIGN KEY (drv_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_drv_activity FOREIGN KEY (drv_activity_id) REFERENCES schedule_activities(act_id),
    CONSTRAINT fk_drv_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 11. schedule_constraint_log
// ─────────────────────────────────────────────────────────────
$tables['schedule_constraint_log'] = "
CREATE TABLE schedule_constraint_log (
    cl_id           BIGINT IDENTITY(1,1) PRIMARY KEY,
    cl_project_id   BIGINT NOT NULL,
    cl_activity_id  BIGINT NOT NULL,
    cl_driver_id    BIGINT NULL,
    cl_needed_by_date DATETIME NULL,
    cl_owner_role   VARCHAR(50) NULL,
    cl_status       VARCHAR(20) NOT NULL DEFAULT 'OPEN',
    cl_notes        TEXT NULL,
    cl_createby     BIGINT NULL,
    cl_createdate   DATETIME NULL DEFAULT GETDATE(),
    cl_modifyby     BIGINT NULL,
    cl_modifydate   DATETIME NULL,
    company_id      BIGINT NOT NULL,
    CONSTRAINT fk_cl_project FOREIGN KEY (cl_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_cl_activity FOREIGN KEY (cl_activity_id) REFERENCES schedule_activities(act_id),
    CONSTRAINT fk_cl_driver FOREIGN KEY (cl_driver_id) REFERENCES schedule_drivers(drv_id),
    CONSTRAINT fk_cl_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 12. schedule_runs
// ─────────────────────────────────────────────────────────────
$tables['schedule_runs'] = "
CREATE TABLE schedule_runs (
    run_id              BIGINT IDENTITY(1,1) PRIMARY KEY,
    run_project_id      BIGINT NOT NULL,
    run_scenario_id     BIGINT NULL,
    run_progress_date   DATETIME NULL,
    run_project_finish  DATETIME NULL,
    run_total_activities INT NOT NULL DEFAULT 0,
    run_critical_count  INT NOT NULL DEFAULT 0,
    run_near_critical_count INT NOT NULL DEFAULT 0,
    run_violations      TEXT NULL,
    run_health_summary  TEXT NULL,
    run_status          VARCHAR(20) NOT NULL DEFAULT 'completed',
    run_computation_ms  INT NULL,
    run_created_by      BIGINT NULL,
    run_created_at      DATETIME NOT NULL DEFAULT GETDATE(),
    company_id          BIGINT NOT NULL,
    CONSTRAINT fk_run_project FOREIGN KEY (run_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT fk_run_scenario FOREIGN KEY (run_scenario_id) REFERENCES schedule_scenarios(scn_id),
    CONSTRAINT fk_run_company FOREIGN KEY (company_id) REFERENCES companies(id)
)";

// ─────────────────────────────────────────────────────────────
// 13. schedule_run_activities
// ─────────────────────────────────────────────────────────────
$tables['schedule_run_activities'] = "
CREATE TABLE schedule_run_activities (
    ra_id                       BIGINT IDENTITY(1,1) PRIMARY KEY,
    ra_run_id                   BIGINT NOT NULL,
    ra_activity_id              BIGINT NOT NULL,
    ra_early_start              DATETIME NULL,
    ra_early_finish             DATETIME NULL,
    ra_late_start               DATETIME NULL,
    ra_late_finish              DATETIME NULL,
    ra_total_float_minutes      INT NULL,
    ra_free_float_minutes       INT NULL,
    ra_is_critical              TINYINT NOT NULL DEFAULT 0,
    ra_driving_predecessor_id   BIGINT NULL,
    ra_driving_constraint_id    BIGINT NULL,
    CONSTRAINT fk_ra_run FOREIGN KEY (ra_run_id) REFERENCES schedule_runs(run_id) ON DELETE CASCADE,
    CONSTRAINT fk_ra_activity FOREIGN KEY (ra_activity_id) REFERENCES schedule_activities(act_id)
)";

// ─────────────────────────────────────────────────────────────
// Create tables in order (respecting FK dependencies)
// ─────────────────────────────────────────────────────────────
$created = 0;
$skipped = 0;

foreach ($tables as $name => $sql) {
    // Check if table already exists
    $check = $pdo->query("SELECT OBJECT_ID('$name', 'U')");
    $exists = $check->fetchColumn();

    if ($exists) {
        echo "SKIP: $name (already exists)\n";
        $skipped++;
        continue;
    }

    try {
        $pdo->exec($sql);
        echo "OK:   $name created\n";
        $created++;
    } catch (PDOException $e) {
        echo "FAIL: $name - " . $e->getMessage() . "\n";
    }
}

// ─────────────────────────────────────────────────────────────
// Add scheduling columns to project_master
// ─────────────────────────────────────────────────────────────
echo "\n--- Adding columns to project_master ---\n";

$columns = [
    'proj_default_calendar_id' => 'BIGINT NULL',
    'proj_scheduling_mode'     => "VARCHAR(20) NULL DEFAULT 'AUTO'",
    'proj_progress_date'       => 'DATETIME NULL',
    'proj_target_finish_date'  => 'DATETIME NULL',
];

foreach ($columns as $col => $type) {
    // Check if column exists
    $check = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'project_master' AND COLUMN_NAME = '$col'");
    if ($check->fetchColumn() > 0) {
        echo "SKIP: project_master.$col (already exists)\n";
        continue;
    }
    try {
        $pdo->exec("ALTER TABLE project_master ADD $col $type");
        echo "OK:   project_master.$col added\n";
    } catch (PDOException $e) {
        echo "FAIL: project_master.$col - " . $e->getMessage() . "\n";
    }
}

// Add FK for default calendar
try {
    $pdo->exec("ALTER TABLE project_master ADD CONSTRAINT fk_proj_default_cal FOREIGN KEY (proj_default_calendar_id) REFERENCES schedule_calendars(cal_id)");
    echo "OK:   FK project_master.proj_default_calendar_id -> schedule_calendars\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'already an object') !== false) {
        echo "SKIP: FK fk_proj_default_cal (already exists)\n";
    } else {
        echo "FAIL: FK fk_proj_default_cal - " . $e->getMessage() . "\n";
    }
}

// ─────────────────────────────────────────────────────────────
// Create indexes
// ─────────────────────────────────────────────────────────────
echo "\n--- Creating indexes ---\n";

$indexes = [
    'idx_cal_company'       => 'CREATE INDEX idx_cal_company ON schedule_calendars(company_id)',
    'idx_cal_project'       => 'CREATE INDEX idx_cal_project ON schedule_calendars(cal_project_id)',
    'idx_wbs_project'       => 'CREATE INDEX idx_wbs_project ON schedule_wbs_nodes(wbs_project_id)',
    'idx_act_project'       => 'CREATE INDEX idx_act_project ON schedule_activities(act_project_id)',
    'idx_act_wbs'           => 'CREATE INDEX idx_act_wbs ON schedule_activities(act_wbs_id)',
    'idx_act_status'        => 'CREATE INDEX idx_act_status ON schedule_activities(act_project_id, act_status)',
    'idx_act_company'       => 'CREATE INDEX idx_act_company ON schedule_activities(company_id)',
    'idx_dep_project'       => 'CREATE INDEX idx_dep_project ON schedule_dependencies(dep_project_id)',
    'idx_dep_pred'          => 'CREATE INDEX idx_dep_pred ON schedule_dependencies(dep_predecessor_id)',
    'idx_dep_succ'          => 'CREATE INDEX idx_dep_succ ON schedule_dependencies(dep_successor_id)',
    'idx_aca_activity'      => 'CREATE INDEX idx_aca_activity ON schedule_activity_actuals(aca_activity_id)',
    'idx_bl_project'        => 'CREATE INDEX idx_bl_project ON schedule_baselines(bl_project_id)',
    'idx_bla_baseline'      => 'CREATE INDEX idx_bla_baseline ON schedule_baseline_activities(bla_baseline_id)',
    'idx_drv_project'       => 'CREATE INDEX idx_drv_project ON schedule_drivers(drv_project_id)',
    'idx_drv_activity'      => 'CREATE INDEX idx_drv_activity ON schedule_drivers(drv_activity_id)',
    'idx_drv_status'        => 'CREATE INDEX idx_drv_status ON schedule_drivers(drv_project_id, drv_status)',
    'idx_cl_project'        => 'CREATE INDEX idx_cl_project ON schedule_constraint_log(cl_project_id)',
    'idx_cl_activity'       => 'CREATE INDEX idx_cl_activity ON schedule_constraint_log(cl_activity_id)',
    'idx_cl_status'         => 'CREATE INDEX idx_cl_status ON schedule_constraint_log(cl_project_id, cl_status)',
    'idx_run_project'       => 'CREATE INDEX idx_run_project ON schedule_runs(run_project_id)',
    'idx_ra_run'            => 'CREATE INDEX idx_ra_run ON schedule_run_activities(ra_run_id)',
    'idx_ra_activity'       => 'CREATE INDEX idx_ra_activity ON schedule_run_activities(ra_activity_id)',
];

foreach ($indexes as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "OK:   $name created\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "SKIP: $name (already exists)\n";
        } else {
            echo "FAIL: $name - " . $e->getMessage() . "\n";
        }
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done! Created $created tables, skipped $skipped existing.\n";
echo str_repeat('=', 60) . "\n";
