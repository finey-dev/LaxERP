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
        if (!Schema::hasTable('machine_repair_requests')) {
            Schema::create('machine_repair_requests', function (Blueprint $table) {
                $table->id();
                $table->integer('machine_id')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->integer('staff_id')->nullable();
                $table->string('description_of_issue')->nullable();
                $table->date('date_of_request')->nullable();
                $table->string('priority_level')->nullable();
                $table->string('status')->default('Pending');
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
        Schema::dropIfExists('machine_repair_requests');
    }
};
