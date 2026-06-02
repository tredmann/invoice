<?php

use App\Models\MasterLineItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMasterLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_line_items', function (Blueprint $table) {
            $table
                ->float('quantity')
                ->change();

            $table
                ->unsignedInteger('price_each')
                ->change();

            $table
                ->unsignedBigInteger('without_tax')
                ->change();

            $table
                ->unsignedBigInteger('with_tax')
                ->change();
        });

        $masterLineItems = MasterLineItem::all();

        foreach ($masterLineItems as $masterLineItem) {
            $masterLineItem->quantity /= 100;
            $masterLineItem->save();
        }
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
