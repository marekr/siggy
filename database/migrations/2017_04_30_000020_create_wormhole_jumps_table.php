
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWormholeJumpsTable extends Migration
{
    /**
     * Run the migrations.
     * @table wormhole_jumps
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wormhole_jumps', function (Blueprint $table) {
            $table->string('wormhole_hash', 33);
            $table->integer('group_id')->unsigned();
            $table->bigInteger('ship_id')->default('0')->unsigned();
            $table->bigInteger('character_id')->default('0')->unsigned();
            $table->bigInteger('origin_id')->default('0')->unsigned();
            $table->bigInteger('destination_id')->default('0')->unsigned();
            $table->dateTime('jumped_at')->nullable()->default(NULL);
            # Indexes
            $table->index(['wormhole_hash','group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('wormhole_jumps');
    }
}
