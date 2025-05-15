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
        if (!Schema::hasTable('asset_licenses'))
        {
            Schema::create('asset_licenses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->integer('category');
                $table->integer('license_number');
                $table->integer('asset');
                $table->date('purchase_date');
                $table->float('purchase_price', 15, 2);
                $table->date('expire_date');
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
        Schema::dropIfExists('asset_licenses');
    }
};
