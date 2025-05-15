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
        if (Schema::hasTable('custom_questions') && !Schema::hasColumn('custom_questions', 'screening_type')) {
            Schema::table('custom_questions', function (Blueprint $table) {
                $table->integer('screening_type')->nullable()->after('is_required');
            });
        }

        if (Schema::hasTable('custom_questions') && !Schema::hasColumn('custom_questions', 'screen_indicator')) {
            Schema::table('custom_questions', function (Blueprint $table) {
                $table->integer('screen_indicator')->nullable()->after('screening_type');
            });
        }

        if (Schema::hasTable('custom_questions') && !Schema::hasColumn('custom_questions', 'rating')) {
            Schema::table('custom_questions', function (Blueprint $table) {
                $table->integer('rating')->nullable()->after('screen_indicator');
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
        Schema::table('custom_questions', function (Blueprint $table) {

        });
    }
};
