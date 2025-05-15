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
        Schema::table('repair_order_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_order_requests', 'repair_technician')) {
                $table->string('repair_technician')->nullable()->after('expiry_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_order_requests', function (Blueprint $table) {
            //
        });
    }
};
