<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('loans', 'balance')) {
                $table->decimal('balance', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('loans', 'monthly_payment')) {
                $table->decimal('monthly_payment', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('loans', 'duration_months')) {
                $table->integer('duration_months')->nullable();
            }
            if (!Schema::hasColumn('loans', 'expected_end_date')) {
                $table->date('expected_end_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'balance', 'monthly_payment', 'duration_months', 'expected_end_date']);
        });
    }
};