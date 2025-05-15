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
        if (!Schema::hasTable('swot_analysis_models')) {
            Schema::create('swot_analysis_models', function (Blueprint $table) {
                $table->id();
                $table->string('name');
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
                $table->longtext('swotanalysismodel_attachments')->nullable();
                $table->longtext('strengths')->nullable();
                $table->longtext('weaknesses')->nullable();
                $table->longtext('opportunities')->nullable();
                $table->longtext('threats')->nullable();
                $table->longtext('notes')->nullable();
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
        Schema::dropIfExists('swot_analysis_models');
    }
};
