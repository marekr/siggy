
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     * @table stats
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->bigInteger('charID')->unsigned();
            $table->string('charName', 255);
            $table->integer('groupID')->default('0')->unsigned();
            $table->integer('chainmap_id')->default('0');
            $table->integer('daystamp')->unsigned()->default('0');
            $table->mediumInteger('adds')->default('0')->unsigned();
            $table->mediumInteger('updates')->default('0')->unsigned();
            $table->mediumInteger('wormholes')->default('0')->unsigned();
            $table->mediumInteger('pos_adds')->default('0')->unsigned();
            $table->mediumInteger('pos_updates')->default('0')->unsigned();
            # Indexes
			$table->primary(['charID','groupID','chainmap_id','daystamp']);
            $table->index(['groupID','chainmap_id','daystamp']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('stats');
    }
}
