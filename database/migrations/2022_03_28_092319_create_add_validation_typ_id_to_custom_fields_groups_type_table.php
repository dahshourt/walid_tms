<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddValidationTypIdToCustomFieldsGroupsTypeTable extends Migration
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
            $table->foreignId('validation_type_id')->nullable();
            $table->foreign('validation_type_id')->references('id')->on('validation_type')->onDelete('cascade');
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
            $table->dropForeign('custom_fields_groups_type_validation_type_id_foreign');
            $table->dropColumn('validation_type_id');
        });
    }
}
