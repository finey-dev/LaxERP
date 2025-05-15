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
        if (!Schema::hasTable('courier_receiver_details')) {
            Schema::create('courier_receiver_details', function (Blueprint $table) {
                $table->id();
                $table->string('tracking_id');
                $table->string('sender_name');
                $table->string('sender_mobileno');
                $table->string('sender_email');
                $table->text('delivery_address');
                $table->string('receiver_name');
                $table->string('receiver_mobileno');
                $table->string('service_type');
                $table->integer('source_branch');
                $table->integer('destination_branch');
                $table->string('payment_type')->nullable();
                $table->string('payment_status')->nullable();
                $table->integer('is_payment_done')->default(0);
                $table->integer('is_courier_delivered')->default(0);
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
        Schema::dropIfExists('courier_receiver_details');
    }
};
