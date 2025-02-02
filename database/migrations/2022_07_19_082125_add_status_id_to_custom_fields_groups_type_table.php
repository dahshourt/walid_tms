<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToCustomFieldsGroupsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_fields_groups_type', function (Blueprint $table) {
            //
            $table->foreignId('status_id')->nullable();
           $table->foreign('id')->references('id')->on('statuses')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_fields_groups_type', function (Blueprint $table) {
            //
            $table->dropForeign('custom_fields_groups_type_status_id_foreign');
            $table->dropColumn('status_id');
        });
    }
}
