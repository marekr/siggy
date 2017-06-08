
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWormholeClassMapTable extends Migration
{
    /**
     * Run the migrations.
     * @table wormhole_class_map
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wormhole_class_map', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('static_id');
            $table->integer('system_class');
            $table->integer('position');
            # Indexes
            $table->unique(['static_id','system_class']);
            $table->index('position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('wormhole_class_map');
    }
}
