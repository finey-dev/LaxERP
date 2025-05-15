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
        if (!Schema::hasTable('visitor_badges')) {

            Schema::create('visitor_badges', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_id');
                $table->string('badge_number')->unique();
                $table->timestamp('issue_date');
                $table->timestamp('return_date');
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
        Schema::dropIfExists('visitor_badges');
    }
};
