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
        if (!Schema::hasTable('swotanalysis_model_comments')) {
            Schema::create('swotanalysis_model_comments', function (Blueprint $table) {
                $table->id();
                $table->integer('swotanalysis_model_id');
                $table->string('file');
                $table->text('comment')->nullable();
                $table->integer('parent')->default('0');
                $table->integer('comment_by')->default('0');
                $table->integer('workspace')->nullable();
                $table->timestamps();
            });
        }
    }


    public function down(): void
    {
        Schema::dropIfExists('swot_analysis_model_comments');
    }
};
