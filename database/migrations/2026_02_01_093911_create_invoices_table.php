<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
                $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
                $table->date('invoice_date');
                $table->date('due_date');
                $table->enum('status', ['draft', 'sent', 'pending', 'partially_paid', 'paid', 'overdue', 'cancelled'])->default('draft');
                $table->decimal('subtotal', 12, 2);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2);
                $table->decimal('amount_paid', 12, 2)->default(0);
                $table->decimal('amount_due', 12, 2);
                $table->text('notes')->nullable();
                $table->text('terms')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('no action');
                $table->index('customer_id');
                $table->index('status');
                $table->index('due_date');
                $table->index('invoice_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};