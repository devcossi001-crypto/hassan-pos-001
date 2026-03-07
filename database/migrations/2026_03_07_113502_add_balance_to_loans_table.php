<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'balance')) {
                $table->decimal('balance', 12, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('loans', 'monthly_payment')) {
                $table->decimal('monthly_payment', 12, 2)->nullable()->after('balance');
            }
            if (!Schema::hasColumn('loans', 'duration_months')) {
                $table->integer('duration_months')->nullable()->after('monthly_payment');
            }
            if (!Schema::hasColumn('loans', 'expected_end_date')) {
                $table->date('expected_end_date')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['balance', 'monthly_payment', 'duration_months', 'expected_end_date']);
        });
    }
};