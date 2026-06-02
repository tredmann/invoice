<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueConstraintFromTenantOwnerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropUnique(['owner_id']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('owner_id')
                ->nullable(false)
                ->change();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table
                ->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
