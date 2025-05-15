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
        if(!Schema::hasTable('request_form_fields'))
        {
            Schema::create('request_form_fields', function (Blueprint $table) {
                $table->id();
                $table->integer('request_id');
                $table->string('name');
                $table->string('type');
                $table->integer('created_by')->default('0');
                $table->integer('workspace')->nullable();
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
        Schema::dropIfExists('request_form_fields');
    }
};
