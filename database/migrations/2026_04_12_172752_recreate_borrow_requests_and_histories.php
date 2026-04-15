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
        Schema::create('borrow_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('item_id');
            $table->foreign('item_id')->references('sr_number')->on('items')->onDelete('cascade');
            $table->string('item_name');
            $table->text('item_description')->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'released'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('borrow_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('item_id');
            $table->foreign('item_id')->references('sr_number')->on('items')->onDelete('cascade');
            $table->string('item_name');
            $table->text('item_description')->nullable();
            $table->decimal('count', 8, 2)->default(1);
            $table->timestamp('borrowed_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->enum('return_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->text('admin_return_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_histories');
        Schema::dropIfExists('borrow_requests');
    }
};
