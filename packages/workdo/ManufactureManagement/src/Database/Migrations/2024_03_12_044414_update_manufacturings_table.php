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
        Schema::table('manufacturings', function (Blueprint $table) {
            if (Schema::hasTable('manufacturings')) {
                $table->integer('collection_center_id')->nullable()->after('bill_of_material_id');
                $table->integer('item_id')->nullable()->after('collection_center_id')->comment('Product&Service Item');
                $table->integer('quantity')->nullable()->after('item_id');
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
        Schema::table('', function (Blueprint $table) {

        });
    }
};
