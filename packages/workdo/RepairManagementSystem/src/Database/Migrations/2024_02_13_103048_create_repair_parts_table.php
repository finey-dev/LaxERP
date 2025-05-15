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
        if (!Schema::hasTable('repair_parts')) {
            Schema::create('repair_parts', function (Blueprint $table) {
                $table->id();
                $table->integer('repair_id')->nullable();
                $table->integer('product_id')->nullable();
                $table->integer('quantity')->nullable();
                $table->string('tax')->nullable();
                $table->float('discount')->default('0.00');
                $table->string('description')->nullable();
                $table->float('price')->default('0.00');
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
        Schema::dropIfExists('repair_parts');
    }
};
