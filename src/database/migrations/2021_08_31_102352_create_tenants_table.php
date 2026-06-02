<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tenants')) {
            Schema::drop('tenants');
        }
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table
                ->unsignedBigInteger('owner_id')
                ->unique()
                ->nullable();
            $table
                ->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->string('testcolumn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
