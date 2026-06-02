<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomerMailReceiversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_mail_receivers', function (Blueprint $table) {
            $table
                ->string('gender')
                ->nullable()
                ->change();
            $table
                ->string('first_name')
                ->nullable()
                ->change();
            $table
                ->string('last_name')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
