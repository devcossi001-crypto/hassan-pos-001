<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'category_name')) {
                $table->string('category_name')->nullable()->after('category_id');
            }
            // Make category_id nullable to allow for free-text categories
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('category_name');
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });
    }
};