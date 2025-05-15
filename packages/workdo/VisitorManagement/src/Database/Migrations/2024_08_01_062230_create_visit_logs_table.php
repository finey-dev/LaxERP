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
        if (!Schema::hasTable('visit_logs')) {
            Schema::create('visit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('visitor_id');
                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->string('duration_of_visit')->nullable();
                $table->string('workspace')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
