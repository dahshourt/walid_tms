<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('event_class')->index()->comment('The event class that triggers this rule, e.g., App\Events\ChangeRequestStatusUpdated.');
            // This JSON column handles your specific status transition requirement
            $table->json('conditions')->nullable()->comment('example: {"from_status_id": 99, "to_status_id": 101}.');
            $table->foreignId('template_id')->constrained('notification_templates')->onDelete('cascade');   
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('notification_rules');
    }
}
