<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['product', 'shipping', 'transaction']);
            $table->enum('amount_type', ['fixed', 'percent']);
            $table->decimal('amount', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_transaction', 10, 2);
            $table->enum('target_audience', ['all', 'user', 'guest']);
            $table->integer('quota');
            $table->integer('limit');
            $table->integer('used');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
