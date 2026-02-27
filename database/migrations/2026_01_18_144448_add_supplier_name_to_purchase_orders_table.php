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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('supplier_name')->nullable()->after('supplier_id');
            $table->foreignId('supplier_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop the index before changing the column in SQL Server
            if (Schema::hasIndex('purchase_orders', 'purchase_orders_supplier_id_index')) {
                $table->dropIndex('purchase_orders_supplier_id_index');
            }
            
            $table->dropColumn('supplier_name');
            // Skip changing back to NOT NULL due to index constraints - leave as nullable
        });
    }
};
