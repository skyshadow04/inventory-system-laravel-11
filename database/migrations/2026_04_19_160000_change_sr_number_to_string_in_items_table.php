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
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('borrow_histories', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->string('sr_number')->change();
        });

        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->string('item_id')->change();
            $table->foreign('item_id')->references('sr_number')->on('items')->onDelete('cascade');
        });

        Schema::table('borrow_histories', function (Blueprint $table) {
            $table->string('item_id')->change();
            $table->foreign('item_id')->references('sr_number')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('borrow_histories', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->integer('sr_number')->change();
        });

        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->integer('item_id')->change();
            $table->foreign('item_id')->references('sr_number')->on('items')->onDelete('cascade');
        });

        Schema::table('borrow_histories', function (Blueprint $table) {
            $table->integer('item_id')->change();
            $table->foreign('item_id')->references('sr_number')->on('items')->onDelete('cascade');
        });
    }
};
