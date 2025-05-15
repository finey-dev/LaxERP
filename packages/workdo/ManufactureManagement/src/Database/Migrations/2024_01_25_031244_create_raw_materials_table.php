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
        if (!Schema::hasTable('raw_materials')) {
            Schema::create('raw_materials', function (Blueprint $table) {
                $table->id();
                $table->integer('collection_center_id');
                $table->string('name')->nullable();
                $table->longText('description')->nullable();
                $table->string('tax')->nullable();
                $table->integer('quantity')->nullable();
                $table->float('price')->default('0.00')->nullable();
                $table->string('unit')->nullable();
                $table->string('image')->nullable();
                $table->integer('status')->default(0)->comment('1 = Active, 0 = Inactive');
                $table->integer('workspace')->nullable();
                $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('raw_materials');
    }
};
