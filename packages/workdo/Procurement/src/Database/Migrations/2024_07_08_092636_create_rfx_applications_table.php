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
        if (!Schema::hasTable('rfx_applications')) {
        Schema::create('rfx_applications', function (Blueprint $table) {
            $table->id();
            $table->integer('rfx');
            $table->string('application_type')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('profile')->nullable();
            $table->string('proposal')->nullable();
            $table->text('cover_letter')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->integer('stage')->default(1);
            $table->integer('order')->default(0);
            $table->text('skill')->nullable();
            $table->integer('rating')->default(0);
            $table->integer('is_archive')->default(0);
            $table->text('custom_question')->nullable();
            $table->integer('is_vendor')->default(0);
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
        Schema::dropIfExists('rfx_applications');
    }
};
