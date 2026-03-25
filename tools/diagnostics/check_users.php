<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== POApp - User Accounts Check ===\n\n";

try {
    require __DIR__.'/vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $connectionString = "sqlsrv:Server=" . $_ENV['DB_HOST'] . ";Database=" . $_ENV['DB_DATABASE'];
    $pdo = new PDO($connectionString, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Check if users table exists
    $tables = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'users'")->fetchAll();
    
    if (empty($tables)) {
        echo "⚠ Users table does not exist yet!\n";
        echo "Run: php artisan migrate\n";
        exit(1);
    }
    
    // Get all users with their companies
    $stmt = $pdo->query("
        SELECT 
            u.id,
            u.name,
            u.email,
            u.u_type,
            u.company_id,
            c.name as company_name,
            CASE 
                WHEN u.u_type = 1 THEN 'Super Admin'
                WHEN u.u_type = 2 THEN 'Administrator'
                ELSE 'Regular User'
            END as role_name
        FROM users u
        LEFT JOIN companies c ON u.company_id = c.id
        WHERE u.deleted_at IS NULL
        ORDER BY u.u_type, u.id
    ");
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "⚠ No users found in database!\n\n";
        echo "Would you like to create test users? (See create_test_users.php)\n";
        exit(1);
    }
    
    echo "Found " . count($users) . " user(s):\n\n";
    
    foreach ($users as $user) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "User ID:     {$user['id']}\n";
        echo "Name:        {$user['name']}\n";
        echo "Email:       {$user['email']}\n";
        echo "Role:        {$user['role_name']} (u_type={$user['u_type']})\n";
        echo "Company ID:  " . ($user['company_id'] ?: 'Not Assigned') . "\n";
        echo "Company:     " . ($user['company_name'] ?: 'Not Assigned') . "\n";
        echo "\n";
    }
    
    echo "\n=== Testing Instructions ===\n\n";
    echo "Default Password (if using Laravel defaults): password\n";
    echo "Hash: \$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi\n\n";
    
    echo "User Roles:\n";
    echo "- Super Admin (u_type=1): Can manage companies, switch companies, full access\n";
    echo "- Administrator (u_type=2): Company-level admin, cannot manage companies\n";
    echo "- Regular User (u_type>2): Limited access within assigned company\n\n";
    
    echo "Login at: http://127.0.0.1:3000/login\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
