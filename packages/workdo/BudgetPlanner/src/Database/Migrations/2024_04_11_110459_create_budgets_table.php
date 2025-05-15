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
        if(!Schema::hasTable('budgets'))
        {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                    $table->string('name');
                    $table->string('period');
                    $table->string('from')->nullable();
                    $table->text('income_data')->nullable();
                    $table->text('expense_data')->nullable();
                    $table->integer('workspace')->nullable();
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
        Schema::dropIfExists('budgets');
    }
};
