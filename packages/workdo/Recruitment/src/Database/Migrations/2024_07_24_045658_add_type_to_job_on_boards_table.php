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
        if (Schema::hasTable('job_on_boards') && !Schema::hasColumn('job_on_boards', 'type')) {
            Schema::table('job_on_boards', function (Blueprint $table) {
                $table->string('type')->nullable()->after('job_type');
            });
        }

        if (Schema::hasTable('job_on_boards') && !Schema::hasColumn('job_on_boards', 'branch_id')) {
            Schema::table('job_on_boards', function (Blueprint $table) {
                $table->integer('branch_id')->nullable()->after('type');
            });
        }

        if (Schema::hasTable('job_on_boards') && !Schema::hasColumn('job_on_boards', 'user_id')) {
            Schema::table('job_on_boards', function (Blueprint $table) {
                $table->integer('user_id')->nullable()->after('branch_id');
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
        Schema::table('job_on_boards', function (Blueprint $table) {

        });
    }
};
