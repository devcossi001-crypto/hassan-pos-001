<?php
// Drop all tables script
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Drop all foreign key constraints first
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME, TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
        WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' AND TABLE_SCHEMA = 'dbo'
    ");
    
    foreach ($constraints as $constraint) {
        echo "Dropping constraint: {$constraint->CONSTRAINT_NAME} on {$constraint->TABLE_NAME}\n";
        DB::statement("ALTER TABLE [{$constraint->TABLE_NAME}] DROP CONSTRAINT [{$constraint->CONSTRAINT_NAME}]");
    }
    
    // Now drop all tables
    $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'dbo' AND TABLE_TYPE = 'BASE TABLE'");
    
    foreach ($tables as $table) {
        echo "Dropping table: {$table->TABLE_NAME}\n";
        DB::statement("DROP TABLE [{$table->TABLE_NAME}]");
    }
    
    echo "\n✓ All tables and constraints dropped successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
