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
        if (!Schema::hasTable('jobapplication_notes')) {
            Schema::create('jobapplication_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('jobapplication_id')->nullable();
                $table->foreign('jobapplication_id')->references('id')->on('job_applications')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('jobapplication_notes');
    }
};
