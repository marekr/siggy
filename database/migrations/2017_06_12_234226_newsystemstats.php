<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Newsystemstats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::drop('apihourlymapdata');

		Schema::create('solarsystem_jumps', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('system_id')->unsigned();
			$table->datetime('date_end');
			$table->datetime('date_start');
			$table->mediumInteger('ship_jumps')->default(0)->unsigned();

			$table->unique(['system_id', 'date_start']);
			$table->index('date_start');
			$table->index('date_end');
		});

		Schema::create('solarsystem_kills', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('system_id')->unsigned();
			$table->datetime('date_end');
			$table->datetime('date_start');
			$table->mediumInteger('ship_kills')->default(0)->unsigned();
			$table->mediumInteger('npc_kills')->default(0)->unsigned();
			$table->mediumInteger('pod_kills')->default(0)->unsigned();

			$table->unique(['system_id', 'date_start']);
			$table->index('date_start');
			$table->index('date_end');
		});
	}

	/**
		* Reverse the migrations.
		*
		* @return void
		*/
	public function down()
	{
		Schema::drop('solarsystem_jumps');
		Schema::drop('solarsystem_kills');

		Schema::create('apihourlymapdata', function (Blueprint $table) {
			$table->integer('systemID')->unsigned()->default('0');
			$table->integer('hourStamp')->unsigned()->default('0');
			$table->mediumInteger('jumps')->default('0')->unsigned();
			$table->smallInteger('kills')->default('0')->unsigned();
			$table->smallInteger('npcKills')->default('0')->unsigned();
			$table->smallInteger('podKills')->default('0')->unsigned();
			# Indexes
			$table->primary(['systemID','hourStamp']);
			$table->index('systemID');
		});
	}
}
