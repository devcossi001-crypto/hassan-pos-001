<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_status', function (Blueprint $table) {
            $table->timestamp('subscription_end_date')->nullable()->comment('Subscription end date');
            $table->boolean('subscription_active')->default(true)->comment('Is subscription active');
        });
    }

    public function down(): void
    {
        Schema::table('system_status', function (Blueprint $table) {
            $table->dropColumn(['subscription_end_date', 'subscription_active']);
        });
    }
};
