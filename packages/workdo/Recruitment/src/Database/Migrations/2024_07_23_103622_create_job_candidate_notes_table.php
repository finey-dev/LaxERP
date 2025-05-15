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
        if (!Schema::hasTable('job_candidate_notes')) {
            Schema::create('job_candidate_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('jobcandidate_id')->nullable();
                $table->foreign('jobcandidate_id')->references('id')->on('job_candidates')->onUpdate('cascade')->onDelete('cascade');
                $table->text('description')->nullable();
                $table->integer('workspace')->nullable();
                $table->integer('created_by');
                $table->timestamps();
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
        Schema::dropIfExists('job_candidate_notes');
    }
};
