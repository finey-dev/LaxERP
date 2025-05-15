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
        if (Schema::hasTable('machine_invoice_payments')) {
            Schema::dropIfExists('machine_invoice_payments');
        }
        if (Schema::hasTable('machine_invoice_diagnoses')) {
            Schema::dropIfExists('machine_invoice_diagnoses');
        }
        if (Schema::hasTable('machine_invoices')) {
            Schema::dropIfExists('machine_invoices');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('machine_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestamps();
        });
    }
};
