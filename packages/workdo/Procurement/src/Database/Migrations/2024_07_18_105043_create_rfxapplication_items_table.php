<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rfx_application_items')) {
            Schema::create('rfx_application_items', function (Blueprint $table) {
                $table->id();
                $table->integer('application_id')->default(0);
                $table->integer('rfx_id')->default(0);
                $table->string('product_type')->nullable();
                $table->integer('product_id')->nullable();
                $table->string('product_tax')->nullable();
                $table->float('product_discount')->default('0.00');
                $table->float('product_price')->default('0.00');
                $table->float('product_total_amount')->default('0.00');
                $table->longText('product_description')->nullable();
                $table->string('rfx_task')->nullable();
                $table->string('rfx_tax')->nullable();
                $table->float('rfx_discount')->default('0.00');
                $table->float('rfx_price')->default('0.00');
                $table->longText('rfx_description')->nullable();
                $table->float('rfx_total_amount')->default('0.00');
                $table->integer('workspace')->nullable();
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
        Schema::dropIfExists('rfxapplication_items');
    }
};
