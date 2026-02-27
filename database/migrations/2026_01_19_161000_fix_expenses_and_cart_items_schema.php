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
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'category_name')) {
                $table->string('category_name')->nullable()->after('category_id');
            }
            // Make category_id nullable to allow for free-text categories
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'session_id')) {
                $table->string('session_id')->nullable()->after('user_id');
                $table->index(['session_id', 'product_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the index before changing the column in SQL Server
             try {
                if (Schema::hasIndex('expenses', 'expenses_category_id_index')) {
                    $table->dropIndex('expenses_category_id_index');
                }
            } catch (\Exception $e) {
                // Index doesn't exist, continue
            }
            
            // Only drop column if it exists
            try {
                if (Schema::hasColumn('expenses', 'category_name')) {
                    $table->dropColumn('category_name');
                }
            } catch (\Exception $e) {
                // Column doesn't exist, continue
            }
            // Skip changing category_id back to NOT NULL due to foreign key constraints
            // It's safe to leave it nullable
        });

        Schema::table('cart_items', function (Blueprint $table) {
            try {
                if (Schema::hasColumn('cart_items', 'session_id')) {
                    $table->dropIndex(['session_id', 'product_id']);
                    $table->dropColumn('session_id');
                }
            } catch (\Exception $e) {
                // Column/index doesn't exist, continue
            }
        });
    }
};
