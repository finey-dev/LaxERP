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
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'biometric_emp_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->integer('biometric_emp_id')->nullable()->after('employee_id');
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
        Schema::table('employees', function (Blueprint $table) {
        });
    }
};
