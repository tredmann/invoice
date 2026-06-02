<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('master_invoice_id');
            $table->foreignId('user_id');
            $table->integer('quantity');
            $table->integer('price_each');
            $table->string('currency');
            $table->bigInteger('without_tax');
            $table->float('tax_rate');
            $table->integer('with_tax');
            $table->string('unit')->nullable();
            $table->string('detail');
            $table->string('detail_plus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_line_items');
    }
}
