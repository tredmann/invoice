<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLegalInfosColumnNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('legal_infos', function (Blueprint $table) {
            $table->renameColumn('field_1', 'registry_court')->change();
            $table->renameColumn('field_2', 'registry_no')->change();
            $table->renameColumn('field_3', 'company_owner')->change();
            $table->renameColumn('field_6', 'tax_no')->change();
            $table->renameColumn('field_7', 'vat_no')->change();
            $table->renameColumn('field_8', 'swift_bic')->change();
            $table->renameColumn('field_9', 'iban')->change();
            $table->renameColumn('field_10', 'bank_institute')->change();
            $table->dropColumn('field_4', 'field_5');
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
