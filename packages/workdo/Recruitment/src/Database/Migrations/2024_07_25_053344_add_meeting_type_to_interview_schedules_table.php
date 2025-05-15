<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('interview_schedules') && !Schema::hasColumn('interview_schedules', 'meeting_type')) {
            Schema::table('interview_schedules', function (Blueprint $table) {
                $table->string('meeting_type')->nullable()->after('comment');
            });
        }

        if (Schema::hasTable('interview_schedules') && !Schema::hasColumn('interview_schedules', 'duration')) {
            Schema::table('interview_schedules', function (Blueprint $table) {
                $table->longText('duration')->nullable()->after('meeting_type');
            });
        }

        if (Schema::hasTable('interview_schedules') && !Schema::hasColumn('interview_schedules', 'start_url')) {
            Schema::table('interview_schedules', function (Blueprint $table) {
                $table->longText('start_url')->nullable()->after('duration');
            });
        }

        if (Schema::hasTable('interview_schedules') && !Schema::hasColumn('interview_schedules', 'join_url')) {
            Schema::table('interview_schedules', function (Blueprint $table) {
                $table->longText('join_url')->nullable()->after('start_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interview_schedules', function (Blueprint $table) {

        });
    }
};
