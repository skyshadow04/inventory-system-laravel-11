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
        Schema::dropIfExists('borrow_histories');
        Schema::dropIfExists('borrow_requests');
        Schema::dropIfExists('items');

        Schema::create('items', function (Blueprint $table) {
            $table->integer('sr_number')->primary();
            $table->string('category_name')->nullable();
            $table->string('item_description');
            $table->string('venue')->nullable();
            $table->string('barcode')->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('total_in', 8, 2)->nullable();
            $table->decimal('total_out', 8, 2)->nullable();
            $table->decimal('total_return', 8, 2)->nullable();
            $table->decimal('quantity_in_hand_current', 8, 2);
            $table->decimal('physical_stock', 8, 2)->nullable();
            $table->string('reconciliation')->nullable();
            $table->decimal('difference', 8, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('availability')->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
