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
        if (!Schema::hasTable('marketing_plans')) {
            Schema::create('marketing_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('status');
                $table->integer('stage');
                $table->integer('challenge');
                $table->string('visibility_type');
                $table->longtext('description');
                $table->longtext('business_summary')->nullable();
                $table->longtext('company_description')->nullable();
                $table->longtext('team')->nullable();
                $table->longtext('business_initiative')->nullable();
                $table->longtext('target_market')->nullable();
                $table->longtext('marketing_channels')->nullable();
                $table->longtext('budget')->nullable();
                $table->string('thumbnail_image')->nullable();
                $table->longtext('notes')->nullable();
                $table->integer('role_id')->default(0);
                $table->string('user_id')->default(0);
                $table->string('video_file')->nullable();
                $table->string('marketing_attachments')->nullable();
                $table->integer('rating')->default(0);
                $table->integer('order')->default(0);
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
        Schema::dropIfExists('marketing_plans');
    }
};
