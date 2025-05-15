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
        if (!Schema::hasTable('service_agreements')) {

            Schema::create('service_agreements', function (Blueprint $table) {
                $table->id();
                $table->string('customer_name');
                $table->longtext('agreement_details');
                $table->date('start_date');
                $table->date('end_date');
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
        Schema::dropIfExists('service_agreements');
    }
};
