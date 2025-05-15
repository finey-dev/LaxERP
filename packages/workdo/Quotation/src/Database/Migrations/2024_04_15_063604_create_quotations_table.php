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
        if(!Schema::hasTable('quotations'))
        {
                Schema::create('quotations', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->unsignedBigInteger('quotation_id')->default('0');
                    $table->unsignedBigInteger('customer_id')->default('0');
                    $table->string('account_type')->default('Accounting');
                    $table->integer('warehouse_id')->default('0');
                    $table->string('quotation')->nullable();
                    $table->date('quotation_date')->nullable();
                    $table->integer('category_id')->default('0');
                    $table->string('quotation_module')->default('account');
                    $table->integer('converted_pos_id')->default('0');
                    $table->integer('is_converted')->default('0');
                    $table->string('quotation_template')->nullable();
                    $table->integer('shipping_display')->default('1');
                    $table->integer('workspace')->nullable();
                    $table->integer('created_by')->default('0');
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
        Schema::dropIfExists('quotations');
    }
};
