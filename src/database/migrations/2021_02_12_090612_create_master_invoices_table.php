<?php

use App\Models\MasterInvoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id');
            $table->foreignId('user_id');
            $table->string('billing_frequency')->nullable();
            $table->date('next_print')->nullable();
            $table->integer('days_till_due')->nullable();
            $table->string('status')->default(MasterInvoice::STATUS_DRAFT);
            $table->string('currency')->nullable();
            $table->integer('total_with_tax')->nullable();
            $table->integer('total_without_tax')->nullable();
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
        Schema::dropIfExists('master_invoices');
    }
}
