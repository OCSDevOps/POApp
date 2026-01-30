<?php
require __DIR__.'/vendor/autoload.php';
file_put_contents('art.log',"autoload\n");
$app = require __DIR__.'/bootstrap/app.php';
file_put_contents('art.log',"app\n", FILE_APPEND);
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
file_put_contents('art.log',"kernel\n", FILE_APPEND);
$input = new ArrayInput(['command' => 'list']);
$output = new ConsoleOutput();
$status = $kernel->handle($input, $output);
file_put_contents('art.log',"after handle\n", FILE_APPEND);
$kernel->terminate($input, $status);
file_put_contents('art.log',"terminated\n", FILE_APPEND);
