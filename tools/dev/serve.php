<?php

$host = '127.0.0.1';
$port = 3000;
$docRoot = __DIR__ . '/public';

echo "Starting PHP built-in server...\n";
echo "Host: $host:$port\n";
echo "Document root: $docRoot\n";
echo "Server will start in 2 seconds...\n";
echo "Access at: http://$host:$port\n\n";

sleep(2);

// Start the built-in PHP server directly
$command = sprintf(
    'php -S %s:%d -t %s %s',
    $host,
    $port,
    escapeshellarg($docRoot),
    escapeshellarg($docRoot . '/index.php')
);

echo "Executing: $command\n\n";

passthru($command, $exitCode);

exit($exitCode);
