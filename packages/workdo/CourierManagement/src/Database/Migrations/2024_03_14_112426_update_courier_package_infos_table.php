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
        if (!Schema::hasTable('courier_package_infos')) {
            Schema::table('courier_package_infos', function (Blueprint $table) {
                if (Schema::hasTable('courier_package_infos')) {
                    $table->string('height')->change();
                    $table->string('width')->change();
                    $table->string('weight')->change();
                }
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
        Schema::table('', function (Blueprint $table) {});
    }
};
