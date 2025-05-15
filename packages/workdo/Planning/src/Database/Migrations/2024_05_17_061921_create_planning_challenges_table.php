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
        if (!Schema::hasTable('planning_challenges')) {
            Schema::create('planning_challenges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('category');
                $table->string('end_date');
                $table->string('position');
                $table->longText('notes');
                $table->longText('explantion')->nullable();
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
        Schema::dropIfExists('planning_challenges');
    }
};
