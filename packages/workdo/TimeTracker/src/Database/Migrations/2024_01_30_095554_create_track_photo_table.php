<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('track_photos')) {
            Schema::create('track_photos', function (Blueprint $table) {
                $table->id();
                $table->integer('track_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->string('img_path')->nullable();
                $table->dateTime('time')->nullable();
                $table->string('workspace_id');
                $table->string('status')->nullable();
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
        Schema::dropIfExists('track_photo');
    }
};
