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
        if (Schema::hasTable('rfx_applications') && !Schema::hasColumn('rfx_applications', 'bid_type')) {
            Schema::table('rfx_applications', function (Blueprint $table) {
                $table->string('bid_type')->nullable()->after('is_vendor');
                $table->integer('bid_total')->nullable()->after('bid_type');
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
        Schema::table('', function (Blueprint $table) {

        });
    }
};
