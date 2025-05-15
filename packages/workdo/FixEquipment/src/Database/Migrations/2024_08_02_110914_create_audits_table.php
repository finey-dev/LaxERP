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
        if (!Schema::hasTable('audits'))
        {
            Schema::create('audits', function (Blueprint $table) {
                $table->id();
                $table->string('audit_title');
                $table->text('audit_data');
                $table->date('audit_date');
                $table->string('asset');
                $table->string('audit_status')->nullable();
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
        Schema::dropIfExists('audits');
    }
};
