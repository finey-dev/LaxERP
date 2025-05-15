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
        if (!Schema::hasTable('collection_center_stocks')) {
            Schema::create('collection_center_stocks', function (Blueprint $table) {
                $table->id();
                $table->integer('warehouse_id')->nullable();
                $table->integer('to_collection_center')->nullable();
                $table->integer('item_id')->nullable();
                $table->integer('quantity')->nullable();
                $table->string('type')->nullable();
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
        Schema::dropIfExists('collection_center_stocks');
    }
};
