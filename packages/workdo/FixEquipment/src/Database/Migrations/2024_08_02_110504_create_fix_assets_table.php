<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('fix_assets'))
        {
            Schema::create('fix_assets', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('asset_image');
                $table->date('purchase_date');
                $table->float('purchase_price', 15, 2);
                $table->integer('location');
                $table->integer('manufacturer');
                $table->integer('category');
                $table->string('serial_number');
                $table->string('model_name');
                $table->integer('supplier');
                $table->integer('depreciation_method');
                $table->integer('status');
                $table->string('license')->nullable();
                $table->string('accessories')->nullable();
                $table->integer('maintenance')->nullable();
                $table->string('description')->nullable();
                $table->integer('audit')->nullable();
                $table->integer('account')->nullable();
                $table->integer('created_by');
                $table->integer('workspace');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fix_assets');
    }
};
