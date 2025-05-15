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
        if (!Schema::hasTable('accessories'))
        {
            Schema::create('accessories', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                    $table->integer('category');
                    $table->integer('asset');
                    $table->integer('manufacturer');
                    $table->integer('supplier');
                    $table->float('price', 15, 2);
                    $table->integer('quantity');
                    $table->string('attachment')->nullable();
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
        Schema::dropIfExists('accessories');
    }
};
