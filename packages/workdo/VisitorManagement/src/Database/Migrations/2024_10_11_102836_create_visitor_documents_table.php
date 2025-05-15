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
        if (!Schema::hasTable('visitor_documents')) {

            Schema::create('visitor_documents', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_id');
                $table->string('document_type');
                $table->string('document_number');
                $table->string('status');
                $table->timestamp('date')->nullable();
                $table->integer('workspace');
                $table->integer('created_by');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_documents');
    }
};
