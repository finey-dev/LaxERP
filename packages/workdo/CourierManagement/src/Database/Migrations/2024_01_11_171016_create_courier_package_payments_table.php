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
        if (!Schema::hasTable('courier_package_payments')) {
            Schema::create('courier_package_payments', function (Blueprint $table) {
                $table->id();
                $table->integer('tracking_id');
                $table->integer('courier_package_id');
                $table->string('payment_type');
                $table->string('payment_status');
                $table->datetime('payment_date')->nullable();
                $table->bigInteger('price')->default(0);
                $table->string('payment_receipt')->nullable();
                $table->integer('is_payment_done')->default(0);
                $table->text('description')->nullable();
                $table->integer('workspace_id');
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
        Schema::dropIfExists('courier_package_payments');
    }
};
