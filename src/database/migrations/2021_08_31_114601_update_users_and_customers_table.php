<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersAndCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (
            Schema::hasColumns('users', [
                'company',
                'street',
                'postal',
                'city',
                'current_team_id',
                'profile_photo_path',
            ]) &&
            ! Schema::hasColumn('users', 'mobile')
        ) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('mobile')->nullable();
                $table->dropColumn(['company', 'street', 'postal', 'city', 'current_team_id', 'profile_photo_path']);
            });
        }

        //        Schema::table('legal_infos', function (Blueprint $table) {
        //            $table->uuid('tenant_id')->after('id');
        //            $table
        //                ->foreign('tenant_id')
        //                ->references('id')
        //                ->on('tenants')
        //                ->onDelete('cascade');
        //        });
        if (Schema::hasColumn('customers', 'tenant_id')) {
            Schema::dropColumns('customers', 'tenant_id');
        }
        Schema::table('customers', function (Blueprint $table) {
            $table
                ->uuid('tenant_id')
                ->after('id')
                ->nullable();
            $table
                ->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');
        });

        //        Schema::table('general_infos', function (Blueprint $table) {
        //            $table->foreignUuid('tenant_id')->after('id');
        //            $table
        //                ->foreign('tenant_id')
        //                ->references('id')
        //                ->on('tenants')
        //                ->onDelete('cascade');
        //        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company');
            $table->string('street');
            $table->string('postal');
            $table->string('city');
            $table->foreignId('current_team_id')->nullable();
            $table->text('profile_photo_path');
        });

        //        Schema::table('legal_infos', function (Blueprint $table) {
        //            $table->dropForeign('tenant_id');
        //        });
        //
        //        Schema::table('customers', function (Blueprint $table) {
        //            $table->dropForeign('tenant_id');
        //        });
        //
        //        Schema::table('general_infos', function (Blueprint $table) {
        //            $table->dropForeign('tenant_id');
        //        });
    }
}
