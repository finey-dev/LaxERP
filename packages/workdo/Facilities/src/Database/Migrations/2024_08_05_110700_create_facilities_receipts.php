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
        if(!Schema::hasTable('facilities_receipts'))
        {
            Schema::create('facilities_receipts', function (Blueprint $table) {
                $table->id();
                $table->integer('booking_id')->nullable();
                $table->integer('client_id')->nullable();
                $table->string('name')->nullable();
                $table->string('service')->nullable();
                $table->string('number')->nullable();
                $table->string('gender')->nullable();
                $table->time('start_time')->default();
                $table->time('end_time')->nullable();
                $table->string('price')->nullable();
                $table->string('payment_type')->nullable();
                $table->integer('workspace')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities_receipts');
    }
};
