<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing database connection...\n\n";

try {
    require __DIR__.'/vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    echo "DB Connection: " . $_ENV['DB_CONNECTION'] . "\n";
    echo "DB Host: " . $_ENV['DB_HOST'] . "\n";
    echo "DB Database: " . $_ENV['DB_DATABASE'] . "\n";
    echo "DB Username: " . ($_ENV['DB_USERNAME'] ?: '(Windows Auth)') . "\n\n";
    
    $connectionString = "sqlsrv:Server=" . $_ENV['DB_HOST'] . ";Database=" . $_ENV['DB_DATABASE'];
    
    echo "Connection string: $connectionString\n\n";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];
    
    if (empty($_ENV['DB_USERNAME'])) {
        echo "Using Windows Authentication...\n";
        $pdo = new PDO($connectionString, null, null, $options);
    } else {
        $pdo = new PDO($connectionString, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $options);
    }
    
    echo "✓ Database connection successful!\n";
    
    $result = $pdo->query("SELECT @@VERSION as version");
    $version = $result->fetch(PDO::FETCH_ASSOC);
    echo "\nSQL Server Version:\n" . $version['version'] . "\n";
    
} catch (\Exception $e) {
    echo "✗ Database connection FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
