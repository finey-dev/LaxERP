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
        if (!Schema::hasTable('todos')) {
            Schema::create('todos', function (Blueprint $table) {
                $table->id();
                $table->integer('related_id');
                $table->string('title');
                $table->string('assigned_to');
                $table->string('description')->default(null)->nullable();
                $table->integer('order')->default(0);
                $table->integer('assign_by');
                $table->string('status')->default('todo');
                $table->string('priority');
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->string('module')->nullable();
                $table->string('sub_module')->nullable();
                $table->integer('workspace_id')->nullable();
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
        Schema::dropIfExists('todos');
    }
};
