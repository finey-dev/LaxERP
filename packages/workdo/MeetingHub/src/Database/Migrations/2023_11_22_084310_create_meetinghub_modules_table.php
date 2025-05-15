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
        if (!Schema::hasTable('meetinghub_modules'))
        {
            Schema::create('meetinghub_modules', function (Blueprint $table) {
                $table->id();
                $table->string('module');
                $table->string('submodule');
                $table->string('model_name')->nullable();
                $table->string('type')->default('company');
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
        Schema::dropIfExists('meetinghub_modules');
    }
};
