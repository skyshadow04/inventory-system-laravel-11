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
        // Update availability based on physical_stock for all existing items
        DB::statement("
            UPDATE items
            SET availability = CASE
                WHEN physical_stock > 0 THEN 'available'
                ELSE 'out_of_stock'
            END
            WHERE physical_stock IS NOT NULL
        ");

        // For items where physical_stock is NULL, set to out_of_stock
        DB::statement("
            UPDATE items
            SET availability = 'out_of_stock'
            WHERE physical_stock IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it updates data
    }
};
