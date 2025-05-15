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
        if (!Schema::hasTable('repair_invoices')) {
            Schema::create('repair_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->integer('repair_id')->nullable();
                $table->float('repair_charge')->default('0.00');
                $table->float('total_amount')->default('0.00');
                $table->integer('status')->default(0)->comment('0 = Pending, 1 = Partialy Paid, 2 = Paid');
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
        Schema::dropIfExists('repair_invoices');
    }
};
