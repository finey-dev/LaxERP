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
        if (!Schema::hasTable('packaging_items')) {
            Schema::create('packaging_items', function (Blueprint $table) {
                $table->id();
                $table->integer('packaging_id');
                $table->integer('raw_material_id');
                $table->integer('old_quantity')->nullable();
                $table->integer('quantity')->nullable();
                $table->string('unit')->nullable();
                $table->float('price')->default('0.00')->nullable();
                $table->string('sub_total')->nullable();
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
        Schema::dropIfExists('packaging_items');
    }
};
