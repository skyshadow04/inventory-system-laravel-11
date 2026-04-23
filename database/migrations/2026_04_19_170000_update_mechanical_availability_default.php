<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the default availability value to match other tables
        DB::statement("ALTER TABLE `items_mech` MODIFY `availability` VARCHAR(255) NOT NULL DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `items_mech` MODIFY `availability` VARCHAR(255) NOT NULL DEFAULT 'Available'");
    }
};