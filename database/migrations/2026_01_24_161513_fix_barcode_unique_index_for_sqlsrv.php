<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run this for SQL Server as MySQL handles NULLs in unique constraints automatically
        // However, we can check driver or just run it blindly if we are sure user is on SQL Server.
        // Given the error was from SQL Server, we tailor this for it.
        
        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        
        if ($driver === 'sqlsrv') {
            Schema::table('products', function (Blueprint $table) {
                // Drop the existing standard unique constraint
                // Note: Laravel names unique indices as table_column_unique by default
                $table->dropUnique('products_barcode_unique');
            });

            // Create a filtered unique index using raw SQL
            \Illuminate\Support\Facades\DB::statement("CREATE UNIQUE INDEX products_barcode_unique ON products(barcode) WHERE barcode IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = \Illuminate\Support\Facades\DB::getDriverName();

        if ($driver === 'sqlsrv') {
            // Drop the filtered index
            \Illuminate\Support\Facades\DB::statement("DROP INDEX products_barcode_unique ON products");

            // Don't try to restore a non-filtered unique index as it will fail with NULL values
            // Just keep the filtered index by recreating it
            \Illuminate\Support\Facades\DB::statement("CREATE UNIQUE INDEX products_barcode_unique ON products(barcode) WHERE barcode IS NOT NULL");
        }
    }
};
