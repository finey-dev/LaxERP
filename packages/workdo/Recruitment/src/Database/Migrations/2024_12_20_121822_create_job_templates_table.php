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
        if (!Schema::hasTable('job_templates')) {
            Schema::create('job_templates', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('description')->nullable();
                $table->text('requirement')->nullable();
                $table->text('terms_and_conditions')->nullable();
                $table->integer('job_id')->nullable();
                $table->integer('branch')->default(0);
                $table->string('location')->nullable();
                $table->string('address')->nullable();
                $table->string('link_type')->nullable();
                $table->string('job_link')->nullable();
                $table->integer('category')->default(0);
                $table->text('skill')->nullable();
                $table->integer('position')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status')->nullable();
                $table->integer('is_post')->default('0');
                $table->string('applicant')->nullable();
                $table->string('visibility')->nullable();
                $table->string('code')->nullable();
                $table->string('custom_question')->nullable();
                $table->string('recruitment_type')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
                $table->string('job_type')->nullable();
                $table->integer('salary_from')->nullable();
                $table->integer('salary_to')->nullable();
                $table->integer('workspace')->nullable();
                $table->integer('created_by');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_templates');
    }
};
