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
        if (Schema::hasTable('rfx_applications') && !Schema::hasColumn('rfx_applications', 'billing_type')) {
            Schema::table('rfx_applications', function (Blueprint $table) {
                $table->string('billing_type')->nullable()->after('bid_total');
                $table->float('bid_total_amount')->default('0.00')->after('billing_type');
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
