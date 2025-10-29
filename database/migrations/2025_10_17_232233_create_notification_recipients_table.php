<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_rule_id')->constrained('notification_rules')->onDelete('cascade');
            $table->enum('channel', ['to', 'cc', 'bcc'])->default('to');
            $table->string('recipient_type')->comment('Type of recipient, e.g., "GROUP", "SPECIFIC_USER", "CR_ASSIGNEE", "STATIC_EMAIL".');
            $table->string('recipient_identifier')->comment('The ID or email address for the recipient type.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_recipients');
    }
}
