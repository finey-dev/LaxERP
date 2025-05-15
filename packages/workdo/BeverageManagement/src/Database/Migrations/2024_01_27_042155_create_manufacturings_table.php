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
        if (!Schema::hasTable('manufacturings')) {
            Schema::create('manufacturings', function (Blueprint $table) {
                $table->id();
                $table->integer('bill_of_material_id');
                $table->date('schedule_date')->nullable();
                $table->float('total')->default('0.00');
                $table->integer('status')->default(0)->comment('1 = Completed, 0 = Pending');
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
        Schema::dropIfExists('manufacturings');
    }
};
