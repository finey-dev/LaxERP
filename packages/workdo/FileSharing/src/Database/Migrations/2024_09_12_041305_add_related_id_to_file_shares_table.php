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
        if (Schema::hasTable('file_shares') && !Schema::hasColumn('file_shares', 'related_id')) {
            Schema::table('file_shares', function (Blueprint $table) {
                $table->integer('related_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('file_shares') && !Schema::hasColumn('file_shares', 'type')) {
            Schema::table('file_shares', function (Blueprint $table) {
                $table->integer('type')->nullable()->after('user_id');
            });
        }

        if (Schema::hasTable('file_shares') && !Schema::hasColumn('file_shares', 'file_name')) {
            Schema::table('file_shares', function (Blueprint $table) {
                $table->integer('file_name')->nullable()->after('file_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_shares', function (Blueprint $table) {
            //
        });
    }
};
