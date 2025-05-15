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
       
        if (!Schema::hasTable('workload_general_settings'))
        {
            Schema::create('workload_general_settings', function (Blueprint $table) {
                $table->id();
                $table->string('user_id');
                $table->json('working_days')->nullable();
                $table->string('total_capacity');
                $table->integer('workspace_id')->nullable();
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
        Schema::dropIfExists('workload_general_settings');
    }
};
