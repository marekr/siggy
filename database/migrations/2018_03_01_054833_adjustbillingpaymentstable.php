<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Adjustbillingpaymentstable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
		Schema::table('billing_payments', function (Blueprint $table) {
			$table->renameColumn('paymentID','id');
			
			$table->renameColumn('eveRefID','ref_id');
			$table->renameColumn('groupID','group_id');
			$table->renameColumn('paymentAmount','amount');
			$table->dateTime('paid_at');
			$table->dateTime('processed_at');

			$table->renameColumn('payeeType','payer_type');
			$table->bigInteger('payer_character_id')->nullable()->unsigned();
			$table->bigInteger('payer_corporation_id')->nullable()->unsigned();
		});

		DB::statement("UPDATE `billing_payments` SET `paid_at`=from_unixtime(paymentTime)");
		DB::statement("UPDATE `billing_payments` SET `processed_at`=from_unixtime(paymentProcessedTime)");

		DB::statement("UPDATE `billing_payments` SET `payer_character_id`=payeeID WHERE payer_type='char'");
		DB::statement("UPDATE `billing_payments` SET `payer_corporation_id`=payeeID WHERE payer_type='corp'");
		DB::statement("UPDATE `billing_payments` SET `payer_character_id`=argID WHERE payer_type='corp'");

		Schema::table('billing_payments', function (Blueprint $table) {
			$table->dropColumn('paymentTime');
			$table->dropColumn('payeeName');
			$table->dropColumn('payeeID');
			$table->dropColumn('paymentProcessedTime');
			$table->dropColumn('argID');
			$table->dropColumn('argName');
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
