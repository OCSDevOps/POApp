<?php
/**
 * Direct Database Setup Script for SQL Server
 */

echo "=== POApp Database Setup ===\n\n";

// Read .env file
$envFile = __DIR__ . '/.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
}

$dbConfig = [
    'host' => $env['DB_HOST'] ?? 'localhost',
    'port' => $env['DB_PORT'] ?? '1433',
    'database' => $env['DB_DATABASE'] ?? 'porder_db',
    'username' => $env['DB_USERNAME'] ?? '',
    'password' => $env['DB_PASSWORD'] ?? '',
];

echo "Connecting to SQL Server at {$dbConfig['host']}...\n";

try {
    // Try Windows Authentication first if no credentials
    if (empty($dbConfig['username'])) {
        $dsn = "sqlsrv:Server={$dbConfig['host']};Database={$dbConfig['database']};TrustServerCertificate=1";
        $pdo = new PDO($dsn);
    } else {
        $dsn = "sqlsrv:Server={$dbConfig['host']},{$dbConfig['port']};Database={$dbConfig['database']};TrustServerCertificate=1";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to database: {$dbConfig['database']}\n\n";
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "1. Ensure SQL Server is running\n";
    echo "2. Check .env file has correct DB_HOST, DB_USERNAME, DB_PASSWORD\n";
    echo "3. For Windows Auth, leave DB_USERNAME and DB_PASSWORD empty\n";
    exit(1);
}

// Helper functions
function tableExists($pdo, $table) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$table'");
    return $stmt->fetchColumn() > 0;
}

function columnExists($pdo, $table, $column) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'");
    return $stmt->fetchColumn() > 0;
}

// Create companies table
if (!tableExists($pdo, 'companies')) {
    echo "Creating companies table...\n";
    $pdo->exec("CREATE TABLE companies (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        name NVARCHAR(255) NOT NULL,
        subdomain NVARCHAR(50) UNIQUE,
        status TINYINT DEFAULT 1,
        address NVARCHAR(500),
        city NVARCHAR(100),
        state NVARCHAR(50),
        zip NVARCHAR(20),
        country NVARCHAR(50) DEFAULT 'USA',
        phone NVARCHAR(20),
        email NVARCHAR(100),
        settings NVARCHAR(MAX),
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE()
    )");
    echo "✓ Companies table created\n";
} else {
    echo "✓ Companies table exists\n";
}

// Add company_id to users if not exists
if (!columnExists($pdo, 'users', 'company_id')) {
    echo "Adding company_id to users table...\n";
    $pdo->exec("ALTER TABLE users ADD company_id BIGINT NULL");
    echo "✓ Added company_id column\n";
} else {
    echo "✓ company_id column exists in users\n";
}

// Add u_type to users if not exists
if (!columnExists($pdo, 'users', 'u_type')) {
    echo "Adding u_type to users table...\n";
    $pdo->exec("ALTER TABLE users ADD u_type INT DEFAULT 2");
    echo "✓ Added u_type column\n";
} else {
    echo "✓ u_type column exists in users\n";
}

// Add u_status to users if not exists
if (!columnExists($pdo, 'users', 'u_status')) {
    echo "Adding u_status to users table...\n";
    $pdo->exec("ALTER TABLE users ADD u_status INT DEFAULT 1");
    echo "✓ Added u_status column\n";
} else {
    echo "✓ u_status column exists in users\n";
}

// Create default company
$stmt = $pdo->query("SELECT COUNT(*) FROM companies");
if ($stmt->fetchColumn() == 0) {
    echo "\nCreating default company...\n";
    $pdo->exec("INSERT INTO companies (name, subdomain, status, city, state, country) 
        VALUES ('Default Company', 'default', 1, 'Anytown', 'CA', 'USA')");
    $stmt = $pdo->query("SELECT @@IDENTITY AS id");
    $companyId = $stmt->fetchColumn();
    echo "✓ Created default company (ID: $companyId)\n";
} else {
    $stmt = $pdo->query("SELECT TOP 1 id, name FROM companies ORDER BY id");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    $companyId = $company['id'];
    echo "✓ Company exists (ID: $companyId, Name: {$company['name']})\n";
}

// Password hash for 'password'
$passwordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

// Setup super admin
echo "\nSetting up super admin user...\n";
$stmt = $pdo->query("SELECT TOP 1 id FROM users WHERE email = 'admin@test.com'");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo "Updating existing admin (ID: {$admin['id']})...\n";
    $stmt = $pdo->prepare("UPDATE users SET u_type = 1, company_id = ?, password = ?, name = 'Super Admin', u_status = 1 WHERE id = ?");
    $stmt->execute([$companyId, $passwordHash, $admin['id']]);
} else {
    echo "Creating new super admin...\n";
    $username = 'admin_' . uniqid();
    $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, u_type, company_id, u_status, created_at, updated_at)
        VALUES ('Super Admin', 'admin@test.com', ?, ?, 1, ?, 1, GETDATE(), GETDATE())");
    $stmt->execute([$username, $passwordHash, $companyId]);
}
echo "✓ Super admin ready (admin@test.com / password)\n";

// Setup regular user
$stmt = $pdo->query("SELECT TOP 1 id FROM users WHERE email = 'user@test.com'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "\nCreating regular test user...\n";
    $username = 'user_' . uniqid();
    $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, u_type, company_id, u_status, created_at, updated_at)
        VALUES ('Test User', 'user@test.com', ?, ?, 2, ?, 1, GETDATE(), GETDATE())");
    $stmt->execute([$username, $passwordHash, $companyId]);
    echo "✓ Created regular user\n";
} else {
    echo "✓ Regular user exists\n";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "SETUP COMPLETE!\n";
echo str_repeat("=", 50) . "\n";
echo "Company ID: $companyId\n\n";
echo "Login Credentials:\n";
echo "  Super Admin: admin@test.com / password\n";
echo "  Regular User:  user@test.com / password\n\n";
echo "Access: http://localhost:8000\n";
