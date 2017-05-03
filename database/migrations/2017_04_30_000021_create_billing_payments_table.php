
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     * @table billing_payments
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_payments', function (Blueprint $table) {
            $table->increments('paymentID');
            $table->bigInteger('eveRefID')->default('0')->unsigned();
            $table->integer('groupID')->default('0');
            $table->integer('paymentTime')->default('0');
            $table->integer('paymentProcessedTime')->default('0');
            $table->decimal('paymentAmount',15,2)->default('0.00');
            $table->bigInteger('payeeID')->default('0')->unsigned();
            $table->string('payeeName', 255)->default('');
            $table->enum('payeeType', ['char', 'corp'])->default('char');
            $table->string('argName', 255)->default('');
            $table->bigInteger('argID')->default('0')->unsigned();
            # Indexes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('billing_payments');
    }
}
