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
        if (!Schema::hasTable('repair_movement_histories')) {
            Schema::create('repair_movement_histories', function (Blueprint $table) {
                $table->id();
                $table->integer('repair_id')->nullable();
                $table->timestamp('date_time')->nullable();
                $table->string('movement_from')->nullable();
                $table->string('movement_to')->nullable();
                $table->string('movement_reason')->nullable();
                $table->integer('workspace')->nullable();
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
        Schema::dropIfExists('repair_movement_histories');
    }
};
