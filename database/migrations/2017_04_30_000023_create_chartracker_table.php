
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartrackerTable extends Migration
{
    /**
     * Run the migrations.
     * @table chartracker
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chartracker', function (Blueprint $table) {
			$table->engine = 'MEMORY';
            $table->bigInteger('charID')->unsigned();
            $table->integer('currentSystemID')->unsigned()->default('0');
            $table->integer('groupID')->unsigned();
            $table->integer('chainmap_id')->unsigned();
            $table->integer('lastBeep')->unsigned()->default('0');
            $table->tinyInteger('broadcast')->default('1');
            $table->integer('shipType')->default('0');
            $table->string('shipName', 255)->default('');
            # Indexes
            $table->primary(['charID','groupID','chainmap_id']);
            $table->index(['groupID','chainmap_id','broadcast']);
            $table->index(['groupID','chainmap_id','broadcast','currentSystemID','lastBeep'], 'query');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
        Schema::drop('chartracker');
    }
}
