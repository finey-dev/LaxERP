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
        Schema::table('interview_schedules', function (Blueprint $table) {
            if (Schema::hasTable('interview_schedules') && !Schema::hasColumn('interview_schedules', 'start_time')) {
                Schema::table('interview_schedules', function (Blueprint $table) {
                    $table->time('start_time')->nullable()->after('meeting_type');
                });
            }

            if (Schema::hasTable('interview_schedules') && !Schema::hasColumn('interview_schedules', 'end_time')) {
                Schema::table('interview_schedules', function (Blueprint $table) {
                    $table->time('end_time')->nullable()->after('start_time');
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_schedules', function (Blueprint $table) {
            //
        });
    }
};
