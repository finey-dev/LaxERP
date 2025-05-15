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
        if (!Schema::hasTable('file_sharing_verifications')) {
            Schema::create('file_sharing_verifications', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->datetime('applied_date')->nullable();
                $table->datetime('action_date')->nullable();
                $table->integer('status')->default(0);
                $table->string('attachment')->nullable();
                $table->integer('workspace')->nullable();
                $table->integer('created_by')->default('0');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_sharing_verifications');
    }
};
