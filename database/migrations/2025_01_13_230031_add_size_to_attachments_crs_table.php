<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeToAttachmentsCrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachements_crs', function (Blueprint $table) {
            $table->bigInteger('size')->nullable()->after('file'); // Adding the 'size' column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachements_crs', function (Blueprint $table) {
            $table->dropColumn('size'); // Rollback by removing the 'size' column
        });
    }
}
