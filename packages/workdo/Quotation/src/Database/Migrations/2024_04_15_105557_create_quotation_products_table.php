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
        if(!Schema::hasTable('quotation_products'))
        {
                Schema::create('quotation_products', function (Blueprint $table) {
                    $table->id();
                    $table->integer('quotation_id')->default('0');
                    $table->string('product_type')->nullable();
                    $table->integer('product_id')->default('0');
                    $table->integer('quantity')->default('0');
                    $table->string('tax')->default('0.00');
                    $table->float('discount')->default('0.00')->nullable();
                    $table->decimal('price',15,2)->default('0.00');
                    $table->text('description')->nullable();
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
        Schema::dropIfExists('quotation_products');
    }
};
