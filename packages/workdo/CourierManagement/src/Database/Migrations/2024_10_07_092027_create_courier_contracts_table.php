<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('courier_contracts')) {

            Schema::create('courier_contracts', function (Blueprint $table) {
                $table->id();
                $table->string('customer_name');
                $table->string('service_type');
                $table->longText('contract_details');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status')->default('Expired');
                $table->integer('workspace');
                $table->integer('created_by');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_contracts');
    }
};
