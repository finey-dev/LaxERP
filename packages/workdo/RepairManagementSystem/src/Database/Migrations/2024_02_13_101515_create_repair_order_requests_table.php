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
        if (!Schema::hasTable('repair_order_requests')) {
            Schema::create('repair_order_requests', function (Blueprint $table) {
                $table->id();
                $table->string('product_name')->nullable();
                $table->integer('product_quantity')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_mobile_no')->nullable();
                $table->date('date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->string('location')->nullable();
                $table->integer('status')->default(0)->comment('0 = Pending, 1= Start Repair, 2= End Repair, 3= Start Testing, 4= End Testing, 5= Irrepairable, 6= Cancel');
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
        Schema::dropIfExists('repair_order_requests');
    }
};
