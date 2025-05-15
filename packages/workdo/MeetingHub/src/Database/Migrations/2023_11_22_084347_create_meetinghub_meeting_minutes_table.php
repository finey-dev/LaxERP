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
        if (!Schema::hasTable('meetinghub_meeting_minutes'))
        {
            Schema::create('meetinghub_meeting_minutes', function (Blueprint $table) {
                $table->id();
                $table->string('meeting_id')->nullable();
                $table->string('caller')->nullable();
                $table->string('contact_user')->nullable();
                $table->string('assign_user')->nullable();
                $table->string('phone_no');
                $table->string('log_type');
                $table->timestamp('call_start_time')->nullable();
                $table->timestamp('call_end_time')->nullable();
                $table->time('duration')->nullable();
                $table->string('priority')->nullable();
                $table->string('status')->nullable();
                $table->string('important')->nullable();
                $table->string('completed')->nullable();
                $table->text('note')->nullable();
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
        Schema::dropIfExists('meetinghub_meeting_minutes');
    }
};
