<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('machines')) {
            Schema::create('machines', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('manufacturer')->nullable();
                $table->string('model')->nullable();
                $table->date('installation_date')->nullable();
                $table->date('last_maintenance_date')->nullable();
                $table->string('description')->nullable();
                $table->string('status')->default('Active');
                $table->integer('workspace')->nullable();
                $table->integer('created_by')->default('0');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machines');
    }
};
