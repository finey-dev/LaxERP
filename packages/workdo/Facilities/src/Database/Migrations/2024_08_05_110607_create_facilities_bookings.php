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
        if(!Schema::hasTable('facilities_bookings'))
        {
            Schema::create('facilities_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('service')->nullable();
                $table->date('date')->nullable();
                $table->integer('client_id')->default(0);
                $table->string('name')->nullable();
                $table->string('number')->nullable();
                $table->string('email')->nullable();
                $table->integer('stage_id')->default(0);
                $table->string('gender')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('person')->nullable();
                $table->string('payment_option')->nullable();
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
        Schema::dropIfExists('facilities_bookings');
    }
};
