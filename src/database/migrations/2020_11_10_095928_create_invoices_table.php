<?php

use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_no')->nullable();
            $table->foreignUuid('customer_id');
            $table->foreignId('user_id');
            $table->string('performed_when')->nullable();
            $table->integer('days_till_due')->nullable();
            $table->date('date_due')->nullable();
            $table->string('status')->default(Invoice::STATUS_DRAFT);
            $table->string('mail_status')->default(Invoice::MAIL_STATUS_NOT_MAILABLE);
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
        Schema::dropIfExists('invoices');
    }
}
