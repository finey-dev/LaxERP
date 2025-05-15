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
        if (Schema::hasTable('job_applications') && !Schema::hasColumn('job_applications', 'job_candidate')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->unsignedBigInteger('job_candidate')->nullable()->after('job');
                $table->foreign('job_candidate')->references('id')->on('job_applications')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('jobs') && !Schema::hasColumn('jobs', 'is_post')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->integer('is_post')->default('0')->after('status');
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
        Schema::table('job_applications', function (Blueprint $table) {

        });
    }
};
