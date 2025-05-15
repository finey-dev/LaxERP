<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rfxs')) {
            Schema::create('rfxs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('description')->nullable();
                $table->text('requirement')->nullable();
                $table->string('location')->nullable();
                $table->integer('category')->default(0);
                $table->text('skill')->nullable();
                $table->integer('position')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status')->nullable();
                $table->string('applicant')->nullable();
                $table->string('visibility')->nullable();
                $table->string('code')->nullable();
                $table->string('custom_question')->nullable();
                $table->text('terms_and_conditions');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
                $table->string('rfx_type')->nullable();
                $table->integer('budget_from')->nullable();
                $table->integer('budget_to')->nullable();
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
        Schema::dropIfExists('rfx');
    }
};
