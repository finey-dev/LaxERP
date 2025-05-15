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
       
        if(!Schema::hasTable('meetinghub_notes'))
        {
            Schema::create('meetinghub_notes', function (Blueprint $table) {
                $table->id();
                $table->integer('meeting_minute_id');
                $table->integer('user_id');
                $table->string('note')->nullable();
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
        Schema::dropIfExists('meetinghub_notes');
    }
};
