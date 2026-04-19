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
        Schema::create('items_eng', function (Blueprint $table) {
            $table->id();
            $table->integer('sr_number');
            $table->string('category_name')->nullable();
            $table->string('item_description')->nullable();
            $table->string('location')->nullable();
            $table->string('venue')->nullable();
            $table->string('barcode')->nullable();
            $table->string('make')->nullable();
            $table->integer('quantity_in_hand')->default(0);
            $table->integer('physical_stock')->default(0);
            $table->text('remarks')->nullable();
            $table->string('availability')->default('Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_eng');
    }
};
