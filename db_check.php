<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "SUCCESS: Database connection is working perfectly.\n";
    echo "Current Database: " . \Illuminate\Support\Facades\DB::getDatabaseName() . "\n";
    
    // Check for required tables
    $tables = ['users', 'books', 'transactions', 'cache'];
    foreach ($tables as $table) {
        if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
            echo "CHECK: Table '$table' exists.\n";
        } else {
            echo "ERROR: Table '$table' is MISSING.\n";
        }
    }
} catch (\Exception $e) {
    echo "FAILURE: Could not connect to the database.\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Check your .env file and ensure MySQL is running.\n";
}
