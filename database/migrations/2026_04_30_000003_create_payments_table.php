<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method', 32);
            $table->string('bayarcash_transaction_id')->nullable()->unique();
            $table->string('bayarcash_exchange_reference')->nullable();
            $table->string('bayarcash_payment_channel')->nullable();
            $table->string('status', 16)->default('pending');
            $table->string('receipt_path')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('raw_callback_payload')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
