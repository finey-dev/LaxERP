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
        if (!Schema::hasTable('meetinghub_tasks'))
        {
            Schema::create('meetinghub_tasks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('meeting_minute_id');
                $table->string('name')->nullable();
                $table->date('date')->nullable();
                $table->time('time')->nullable();
                $table->string('priority')->nullable();
                $table->string('status')->nullable();
                $table->string('workspace_id')->default(0);
                $table->string('created_by')->default(0);
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
        Schema::dropIfExists('meetinghub_tasks');
    }
};
