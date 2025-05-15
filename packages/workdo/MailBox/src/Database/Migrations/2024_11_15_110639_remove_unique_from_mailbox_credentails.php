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
        if (Schema::hasTable('mailbox_credentails')) {

            Schema::table('mailbox_credentails', function (Blueprint $table) {
                $table->dropUnique(['emailbox_mail_driver']);
                $table->dropUnique(['emailbox_mail_host']);
                $table->dropUnique(['emailbox_outgoing_port']);
                $table->dropUnique(['emailbox_incoming_port']);
                $table->dropUnique(['emailbox_mail_username']);
                $table->dropUnique(['emailbox_mail_from_address']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */

    public function down()
    {
        Schema::table('mailbox_credentails', function (Blueprint $table) {
            $table->unique(['emailbox_mail_driver']);
            $table->unique(['emailbox_mail_host']);
            $table->unique(['emailbox_outgoing_port']);
            $table->unique(['emailbox_incoming_port']);
            $table->unique(['emailbox_mail_username']);
            $table->unique(['emailbox_mail_from_address']);
        });
    }
};
