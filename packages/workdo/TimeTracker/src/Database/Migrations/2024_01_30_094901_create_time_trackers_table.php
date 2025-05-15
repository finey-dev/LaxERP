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
        if(!Schema::hasTable('time_trackers'))
        {
            Schema::create('time_trackers', function (Blueprint $table) {
                $table->id();
                $table->integer('project_id')->nullable();
                $table->integer('task_id')->nullable();
                $table->string('workspace_id');
                $table->string('name')->nullable();
                $table->dateTime('start_time')->nullable();
                $table->dateTime('end_time')->nullable();
                $table->string('total_time')->default(0);
                $table->string('is_active')->default(1);
                $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('time_trackers');
    }
};
