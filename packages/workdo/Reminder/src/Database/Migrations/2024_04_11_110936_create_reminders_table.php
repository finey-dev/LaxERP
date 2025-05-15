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
        if (!Schema::hasTable('reminders'))
        {
            Schema::create('reminders', function (Blueprint $table) {
                $table->id();
                $table->string('date_select')->nullable();
                $table->date('date')->nullable();
                $table->integer('day')->default('0');
                $table->string('action')->nullable();
                $table->string('module')->nullable();
                $table->string('module_value')->nullable();
                $table->json('to')->nullable();
                $table->longText('message')->nullable();
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
        Schema::dropIfExists('reminders');
    }
};
