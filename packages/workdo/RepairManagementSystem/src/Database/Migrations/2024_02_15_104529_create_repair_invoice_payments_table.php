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
        if(!Schema::hasTable('repair_invoice_payments'))
        {
            Schema::create('repair_invoice_payments', function (Blueprint $table) {
                $table->id();
                $table->integer('invoice_id');
                $table->integer('repair_id');
                $table->float('amount')->default('0.00');
                $table->string('payment_type')->default('Manually');
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
        Schema::dropIfExists('repair_invoice_payments');
    }
};
