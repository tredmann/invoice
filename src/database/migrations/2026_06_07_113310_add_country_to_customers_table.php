<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: add as nullable
        Schema::table('customers', function (Blueprint $table) {
            $table->string('country', 2)->nullable()->after('city');
        });

        // Step 2: backfill existing rows
        DB::table('customers')->whereNull('country')->update(['country' => 'DE']);

        // Step 3: make non-nullable with default
        Schema::table('customers', function (Blueprint $table) {
            $table->string('country', 2)->default('DE')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
