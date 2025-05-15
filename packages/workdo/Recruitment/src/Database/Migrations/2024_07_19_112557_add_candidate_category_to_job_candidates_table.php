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
        if (Schema::hasTable('job_candidates') && !Schema::hasColumn('job_candidates', 'candidate_category')) {
            Schema::table('job_candidates', function (Blueprint $table) {
                $table->unsignedBigInteger('candidate_category')->nullable()->after('id');
                $table->foreign('candidate_category')->references('id')->on('job_candidates')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('job_candidates', function (Blueprint $table) {
        });
    }
};
