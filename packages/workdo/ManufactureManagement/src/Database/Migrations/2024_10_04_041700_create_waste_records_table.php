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
        if (!Schema::hasTable('waste_records')) {
            Schema::create('waste_records', function (Blueprint $table) {
                $table->id();
                $table->string('item_id');
                $table->date('waste_date');
                $table->string('waste_categories');
                $table->integer('quantity');
                $table->string('reason');
                $table->longText('comments');
                $table->integer('workspace')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_records');
    }
};
