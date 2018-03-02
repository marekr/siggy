<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('billing_charges', function (Blueprint $table) {
			$table->renameColumn('chargeID','id');
			
			$table->renameColumn('groupID','group_id');

			$table->renameColumn('memberCount','member_count');

			$table->dateTime('charged_at');
		});

		DB::statement("UPDATE `billing_charges` SET `charged_at`=from_unixtime(`date`)");

		Schema::table('billing_charges', function (Blueprint $table) {
			$table->dropColumn('date');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('billing_charges', function (Blueprint $table) {
			$table->renameColumn('id','chargeID');
			
			$table->renameColumn('group_id','groupID');

			$table->renameColumn('member_count','memberCount');

			$table->integer('date');
		});

		DB::statement("UPDATE `billing_charges` SET `date`=UNIX_TIMESTAMP(`charged_at`)");

		Schema::table('billing_charges', function (Blueprint $table) {
			$table->dropColumn('charged_at');
		});
    }
}
