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
        if (!Schema::hasTable('trackingstatuses')) {
            Schema::create('trackingstatuses', function (Blueprint $table) {
                $table->id();
                $table->string('icon_name')->nullable();
                $table->string('status_color')->nullable();
                $table->integer('order')->default(0);
                $table->string('status_name');
                $table->integer('workspace');
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
        Schema::dropIfExists('trackingstatuses');
    }
};
