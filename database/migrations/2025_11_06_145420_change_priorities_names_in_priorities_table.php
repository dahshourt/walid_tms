<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Set names explicitly by ID
        DB::table('priorities')->where('id', 1)->update(['name' => 'Low']);
        DB::table('priorities')->where('id', 2)->update(['name' => 'Normal']);
        DB::table('priorities')->where('id', 3)->update(['name' => 'High']);
        DB::table('priorities')->where('id', 4)->update(['name' => 'Critical']);
    }

    public function down(): void
    {
        // Restore original names by ID
        DB::table('priorities')->where('id', 1)->update(['name' => 'Normal']);
        DB::table('priorities')->where('id', 2)->update(['name' => 'High']);
        DB::table('priorities')->where('id', 3)->update(['name' => 'Critical']);
        DB::table('priorities')->where('id', 4)->update(['name' => 'Low']);
    }
};
