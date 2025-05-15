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
        if(!Schema::hasTable('facilities_workings'))
        {
            Schema::create('facilities_workings', function (Blueprint $table) {
                $table->id();
                $table->time('opening_time');
                $table->time('closing_time');
                $table->time('breck_start')->nullable();
                $table->time('breck_end')->nullable();
                $table->string('day_of_week');
                $table->string('holiday_setting');
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
        Schema::dropIfExists('facilities_workings');
    }
};
