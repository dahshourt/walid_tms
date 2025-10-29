<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_rule_id')->nullable()->constrained('notification_rules')->onDelete('set null');
            $table->foreignId('template_id')->nullable()->constrained('notification_templates')->onDelete('set null');

            // What was sent
            $table->string('event_class')->index();
            $table->json('event_data')->nullable();
            $table->string('subject');
            $table->longText('body');

            // Recipients
            $table->json('recipients_to');
            $table->json('recipients_cc')->nullable();
            $table->json('recipients_bcc')->nullable();

            // Status tracking
            $table->enum('status', ['pending', 'sent', 'failed', 'queued'])->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('retry_count')->default(0);

            // Reference data (for tracking what triggered it)
            $table->string('related_model_type')->nullable()->index();
            $table->unsignedBigInteger('related_model_id')->nullable()->index();
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
        Schema::dropIfExists('notification_logs');
    }
}
