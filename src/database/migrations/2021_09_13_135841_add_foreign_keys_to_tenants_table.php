<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('tenants', 'legal_info_id')) {
            Schema::dropColumns('tenants', 'legal_info_id');
            echo 'dropping legal_info_id on tenants', PHP_EOL;
        }

        if (Schema::hasColumn('tenants', 'general_info_id')) {
            Schema::dropColumns('tenants', 'general_info_id');
            echo 'dropping general_info_id on tenants', PHP_EOL;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->uuid('legal_info_id')->nullable();
            $table
                ->foreign('legal_info_id')
                ->references('id')
                ->on('legal_infos')
                ->onDelete('set null');
            $table->uuid('general_info_id')->nullable();
            $table
                ->foreign('general_info_id')
                ->references('id')
                ->on('general_infos')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            //
        });
    }
}
