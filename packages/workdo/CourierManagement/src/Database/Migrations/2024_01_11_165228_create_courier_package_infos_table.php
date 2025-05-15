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
        if (!Schema::hasTable('courier_package_infos')) {
            Schema::create('courier_package_infos', function (Blueprint $table) {
                $table->id();
                $table->integer('tracking_id');
                $table->string('package_title');
                $table->text('package_description');
                $table->float('height');
                $table->float('width');
                $table->float('weight');
                $table->string('package_category');
                $table->string('tracking_status');
                $table->text('tracking_status_log');
                $table->bigInteger('price');
                $table->date('expected_delivery_date');
                $table->integer('workspace_id');
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
        Schema::dropIfExists('courier_package_infos');
    }
};
