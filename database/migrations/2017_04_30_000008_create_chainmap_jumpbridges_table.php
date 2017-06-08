
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChainmapJumpbridgesTable extends Migration
{
    /**
     * Run the migrations.
     * @table chainmap_jumpbridges
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chainmap_jumpbridges', function (Blueprint $table) {
            $table->string('hash', 32);
            $table->integer('to_system_id');
            $table->integer('from_system_id');
            $table->integer('group_id');
            $table->integer('chainmap_id');
            # Indexes
            $table->primary(['hash','group_id','chainmap_id']);
            $table->index(['chainmap_id','group_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('chainmap_jumpbridges');
    }
}
