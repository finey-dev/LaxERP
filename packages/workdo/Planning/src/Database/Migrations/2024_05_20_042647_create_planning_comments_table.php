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
        if (!Schema::hasTable('planning_comments')) {
            Schema::create('planning_comments', function (Blueprint $table) {
                $table->id();
                $table->integer('charter_id');
                $table->string('file');
                $table->text('comment')->nullable();
                $table->integer('parent')->default('0');
                $table->integer('comment_by')->default('0');
                $table->integer('workspace')->nullable();
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
        Schema::dropIfExists('planning_comments');
    }
};
