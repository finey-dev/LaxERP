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
        if (!Schema::hasTable('planning_charters')) {
        Schema::create('planning_charters', function (Blueprint $table) {
            $table->id();
            $table->string('charter_name');
            $table->longtext('dsescription');
            $table->integer('status');
            $table->integer('stage');
            $table->integer('challenge');
            $table->string('visibility_type');
            $table->integer('rating')->default(0);
            $table->string('thumbnail_image')->nullable();
            $table->string('video_file')->nullable();
            $table->string('user_id')->default(0);
            $table->integer('role_id')->default(0);
            $table->integer('order')->default(0);
            $table->longtext('charter_attachments')->nullable();
            $table->longtext('organisational_effects')->nullable();
            $table->longtext('goal_description')->nullable();
            $table->longtext('notes')->nullable();
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
        Schema::dropIfExists('planning_charters');
    }
};
