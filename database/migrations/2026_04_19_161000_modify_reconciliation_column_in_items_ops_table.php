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
        // Change reconciliation to a text field so operation imports can store notes like "Temporary usage".
        DB::statement('ALTER TABLE `items_ops` MODIFY `reconciliation` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `items_ops` MODIFY `reconciliation` INT DEFAULT 0 NOT NULL');
    }
};
