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
        Schema::table('bill_of_materials', function (Blueprint $table) {
            if (Schema::hasTable('bill_of_materials')) {
                $table->integer('item_id')->nullable()->after('quantity');
                $table->integer('collection_center_id')->nullable()->after('item_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_materials', function (Blueprint $table) {
            $table->dropColumn('item_id');
            $table->dropColumn('collection_center_id');
        });
    }
};
