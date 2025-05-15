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
        if (!Schema::hasTable('equipment_maintenance'))
        {
            Schema::create('equipment_maintenance', function (Blueprint $table) {
                $table->id();
                $table->string('maintenance_type');
                $table->integer('asset');
                $table->float('price', 15, 2);
                $table->date('maintenance_date');
                $table->string('description')->nullable();
                $table->integer('account')->nullable();
                $table->integer('created_by');
                $table->integer('workspace');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance');
    }
};
