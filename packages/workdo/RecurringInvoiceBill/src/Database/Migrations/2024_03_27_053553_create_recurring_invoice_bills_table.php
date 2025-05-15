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
        if (!Schema::hasTable('recurring_invoice_bills'))
        {
            Schema::create('recurring_invoice_bills', function (Blueprint $table) {
                $table->id();
                // invoice and bill id
                $table->integer('invoice_id')->nullable();
                $table->string('recurring_type')->nullable();
                $table->string('cycles')->nullable();
                $table->string('recurring_duration')->nullable();
                $table->string('day_type')->nullable();
                $table->string('count')->nullable();
                $table->date('modify_date')->nullable();
                $table->date('modify_due_date')->nullable();
                $table->string('pending_cycle')->nullable();
                $table->string('dublicate_invoice')->nullable();
                $table->integer('workspace')->nullable();
                $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('recurring_invoice_bills');
    }
};
