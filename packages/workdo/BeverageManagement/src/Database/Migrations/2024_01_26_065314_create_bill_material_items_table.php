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
        if (!Schema::hasTable('bill_material_items')) {
            Schema::create('bill_material_items', function (Blueprint $table) {
                $table->id();
                $table->integer('bill_of_material_id');
                $table->integer('raw_material_id');
                $table->string('unit')->nullable();
                $table->integer('quantity')->nullable();
                $table->string('price')->nullable();
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
        Schema::dropIfExists('bill_material_items');
    }
};
