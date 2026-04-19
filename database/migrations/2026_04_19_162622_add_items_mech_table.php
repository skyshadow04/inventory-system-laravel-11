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
            Schema::create('items_mech', function (Blueprint $table) {
                $table->id();
                $table->string('sr_no')->nullable();
                $table->string('category_name')->nullable();
                $table->string('description')->nullable();
                $table->integer('total_qty')->default(0);
                $table->string('precision_measurement_class_1')->nullable();
                $table->string('location')->nullable();
                // Sub-locations / zones
                $table->integer('w_18_b')->default(0);
                $table->integer('w_17')->default(0);
                $table->integer('w_18_a_compressor_area')->default(0);
                $table->integer('w_18_a_bearing_area')->default(0);
                $table->integer('balance_qty_in_store')->default(0);
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
            Schema::dropIfExists('items_mech');
        }
};
