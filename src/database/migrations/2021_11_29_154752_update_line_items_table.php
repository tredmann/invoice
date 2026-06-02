<?php

use App\Models\LineItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_items', function (Blueprint $table) {
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

        $lineItems = LineItem::all();

        foreach ($lineItems as $lineItem) {
            $lineItem->quantity /= 100;
            $lineItem->save();
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
