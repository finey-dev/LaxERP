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
        if (!Schema::hasTable('courier_branch')) {
            Schema::create('courier_branch', function (Blueprint $table) {
                $table->id();
                $table->string('branch_name');
                $table->text('branch_location');
                $table->string('city');
                $table->string('state');
                $table->string('country');
                $table->integer('workspace');
                $table->integer('created_by');
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
        Schema::dropIfExists('courier_branch');
    }
};
