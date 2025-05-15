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
        if (!Schema::hasTable('equipment_locations'))
        {
            Schema::create('equipment_locations', function (Blueprint $table) {
                $table->id();
                $table->string('location_name');
                    $table->string('address');
                    $table->string('attachment');
                    $table->string('location_description')->nullable();
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
        Schema::dropIfExists('equipment_locations');
    }
};
