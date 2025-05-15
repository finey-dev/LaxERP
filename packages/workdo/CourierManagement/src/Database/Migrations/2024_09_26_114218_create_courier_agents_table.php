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
        if (!Schema::hasTable('courier_agents')) {

            Schema::create('courier_agents', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone');
                $table->string('email');
                $table->longtext('address')->nullable();
                $table->integer('branch_id');
                $table->string('status')->default('Inactive');
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
        Schema::dropIfExists('courier_agents');
    }
};
