
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApihourlymapdataTable extends Migration
{
    /**
     * Run the migrations.
     * @table apihourlymapdata
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('apihourlymapdata');
    }
}
