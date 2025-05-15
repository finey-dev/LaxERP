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
        if(Schema::hasTable('assets'))
        {
            Schema::table('assets', function (Blueprint $table) {
                if (!Schema::hasColumn('assets', 'location')) {
                    $table->text('location')->nullable()->after('purchase_cost');
                }
                if (!Schema::hasColumn('assets', 'category')) {
                    $table->text('category')->nullable()->after('location');
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
        Schema::table('assets', function (Blueprint $table) {

        });
    }
};
