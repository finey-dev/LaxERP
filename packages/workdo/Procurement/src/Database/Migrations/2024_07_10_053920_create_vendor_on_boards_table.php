<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('vendor_on_boards')) {
            Schema::create('vendor_on_boards', function (Blueprint $table) {
                $table->id();
                $table->integer('application');
                $table->date('joining_date')->nullable();
                $table->string('status')->nullable();
                $table->string('rfx_type')->nullable();
                $table->integer('days_of_week')->nullable();
                $table->integer('budget')->nullable();
                $table->string('budget_type')->nullable();
                $table->string('budget_duration')->nullable();
                $table->integer('convert_to_vendor')->default(0);
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
        Schema::dropIfExists('vendor_on_boards_applicant');
    }
};
