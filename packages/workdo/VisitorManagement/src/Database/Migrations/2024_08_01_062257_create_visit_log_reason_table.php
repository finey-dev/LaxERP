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
        if (!Schema::hasTable('visit_log_reason')) {
            Schema::create('visit_log_reason', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('visit_log_id');
                $table->unsignedBigInteger('visit_reason_id');
                $table->unsignedBigInteger('visitor_id');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_log_reason');
    }
};
