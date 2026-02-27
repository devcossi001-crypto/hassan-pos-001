<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true)->comment('System activation status');
            $table->string('status_reason')->nullable()->comment('Reason for deactivation');
            $table->foreignId('deactivated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_status');
    }
};
