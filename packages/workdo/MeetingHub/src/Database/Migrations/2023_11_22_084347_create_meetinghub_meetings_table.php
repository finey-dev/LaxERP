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
        if (!Schema::hasTable('meetinghub_meetings'))
        {
            Schema::create('meetinghub_meetings', function (Blueprint $table) {
                $table->id();
                $table->string('sub_module')->nullable();
                $table->string('caller')->nullable();
                $table->string('user_id')->nullable();
                $table->string('lead_id')->nullable();
                $table->integer('meeting_type')->nullable();
                $table->string('location')->nullable();
                $table->string('subject')->nullable();
                $table->text('description')->nullable();
                $table->string('created_by')->default(0);
                $table->string('workspace_id')->default(0);
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
        Schema::dropIfExists('meetinghub_meetings');
    }
};
