<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number', 32)->unique();
            $table->date('period_month');
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->date('due_date');
            $table->string('status', 16)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_month']);
            $table->index('status');
            $table->index('due_date');
            $table->index('period_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
