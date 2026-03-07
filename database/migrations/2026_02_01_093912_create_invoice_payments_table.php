<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoice_payments')) {
            Schema::create('invoice_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('payment_date');
                $table->enum('payment_method', ['cash', 'mpesa', 'card', 'bank_transfer', 'cheque'])->default('cash');
                $table->string('reference_number')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('received_by');
                $table->timestamps();

                $table->foreign('received_by')->references('id')->on('users')->onDelete('no action');
                $table->index('invoice_id');
                $table->index('payment_date');
                $table->index('payment_method');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};