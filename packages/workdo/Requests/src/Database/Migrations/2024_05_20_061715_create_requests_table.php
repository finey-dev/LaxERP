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
        if(!Schema::hasTable('requests'))
        {
            Schema::create('requests', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->integer('category_id');
                $table->integer('subcategory_id');
                $table->string('active')->default(0)->comment('on = Active, off = Inactive');
                $table->string('module_type')->nullable();
                $table->string('is_converted')->default(0);
                $table->string('layouts')->default('form-one');
                $table->string('theme_color')->default('Formlayout1-v1');
                $table->integer('workspace')->nullable();
                $table->integer('created_by')->default('0');
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
        Schema::dropIfExists('requests');
    }
};
