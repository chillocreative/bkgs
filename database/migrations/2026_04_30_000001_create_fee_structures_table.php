<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->unsignedTinyInteger('due_day')->default(5);
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->unsignedSmallInteger('late_fee_grace_days')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
