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
        if (Schema::hasTable('faqs')) {
            Schema::table('faqs', function (Blueprint $table) {
                if (Schema::hasColumns('faqs', ['description'])) {
                    $table->longText('description')->change();
                }
            });
        }
        if (Schema::hasTable('conversions')) {
            Schema::table('conversions', function (Blueprint $table) {
                if (Schema::hasColumns('conversions', ['description'])) {
                    $table->longText('description')->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets_infos', function (Blueprint $table) {
            //
        });
    }
};
