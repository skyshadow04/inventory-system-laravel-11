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
            $table->enum('return_status', ['pending', 'approved', 'rejected'])->nullable()->after('returned_at');
            $table->timestamp('return_requested_at')->nullable()->after('return_status');
            $table->text('admin_return_notes')->nullable()->after('return_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_histories', function (Blueprint $table) {
            $table->dropColumn(['return_status', 'return_requested_at', 'admin_return_notes']);
        });
    }
};
