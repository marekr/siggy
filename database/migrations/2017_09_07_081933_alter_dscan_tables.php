<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDscanTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('dscan', function (Blueprint $table) {
			$table->renameColumn('dscan_id','id');
			
			$table->renameColumn('dscan_title','title');
			$table->string('dscan_title', 255)->change();

			$table->timestamps();

			$table->renameColumn('dscan_added_by','added_by');
		});

		DB::statement("UPDATE `dscan` SET `created_at`=from_unixtime(dscan_date)");


		Schema::table('dscan', function (Blueprint $table) {
			$table->dropColumn('dscan_date');
		});

		Schema::table('dscan_records', function (Blueprint $table) {
			$table->bigIncrements('id');
		});
	}

	/**
		* Reverse the migrations.
		*
		* @return void
		*/
	public function down()
	{
		Schema::table('dscan', function (Blueprint $table) {
			$table->renameColumn('id','dscan_id');
			
			$table->renameColumn('title','dscan_title');

			$table->dropTimestamps();

			$table->renameColumn('added_by','dscan_added_by');
		});

		Schema::table('dscan_records', function (Blueprint $table) {
			$table->bigIncrements('id');
		});
	}
}
