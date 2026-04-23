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
        Schema::table('borrow_histories', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['item_id']);
            // Change item_id to string to accommodate different item types
            $table->string('item_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_histories', function (Blueprint $table) {
            // Change back to integer
            $table->integer('item_id')->change();
            // Recreate the foreign key constraint
            $table->foreign('item_id')->references('sr_number')->on('items')->onDelete('cascade');
        });
    }
};
