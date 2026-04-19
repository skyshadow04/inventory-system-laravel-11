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
        Schema::create('items_ops', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('sr_no')->nullable();
            $table->string('category_name')->nullable();
            $table->string('item_description')->nullable();
            $table->string('location')->nullable();
            $table->string('venue')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->string('supplier')->nullable();
            $table->integer('total_in')->default(0);
            $table->integer('total_out')->default(0);
            $table->integer('total_return')->default(0);
            $table->integer('quantity_in_hand')->default(0);
            $table->integer('physical_stock')->default(0);
            $table->integer('reconciliation')->default(0);
            $table->integer('difference')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_ops');
    }
};
