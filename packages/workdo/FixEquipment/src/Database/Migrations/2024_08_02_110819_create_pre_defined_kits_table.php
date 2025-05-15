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
        if (!Schema::hasTable('pre_defined_kits'))
        {
            Schema::create('pre_defined_kits', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->integer('asset');
                $table->integer('component');
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
        Schema::dropIfExists('pre_defined_kits');
    }
};
