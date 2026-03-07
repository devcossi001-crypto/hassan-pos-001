<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('loans')) {
            Schema::create('loans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->string('customer_id_number')->nullable();
                $table->decimal('total_amount', 12, 2);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('balance', 12, 2);
                $table->decimal('monthly_payment', 12, 2)->nullable();
                $table->integer('duration_months')->nullable();
                $table->date('start_date');
                $table->date('expected_end_date')->nullable();
                $table->enum('status', ['active', 'completed', 'defaulted'])->default('active');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();

                $table->index('customer_phone');
                $table->index('status');
            });
        }

        if (!Schema::hasTable('loan_payments')) {
            Schema::create('loan_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('payment_date');
                $table->string('payment_method')->default('cash');
                $table->string('reference')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('received_by')->constrained('users');
                $table->timestamps();

                $table->index(['loan_id', 'payment_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
        Schema::dropIfExists('loans');
    }
};