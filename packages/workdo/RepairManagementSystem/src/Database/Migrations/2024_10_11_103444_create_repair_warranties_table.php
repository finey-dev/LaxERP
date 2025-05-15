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
        if(!Schema::hasTable('repair_warranties'))
        {
            Schema::create('repair_warranties', function (Blueprint $table) {
                $table->id();
                $table->integer('repair_order_id')->nullable();
                $table->integer('part_id')->nullable();
                $table->string('warranty_number')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('warranty_terms')->nullable();
                $table->string('claim_status')->nullable();
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
        Schema::dropIfExists('repair_warranties');
    }
};
