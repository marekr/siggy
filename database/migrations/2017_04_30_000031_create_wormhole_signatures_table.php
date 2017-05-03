
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWormholeSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     * @table wormhole_signatures
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wormhole_signatures', function (Blueprint $table) {
            $table->string('wormhole_hash', 32);
            $table->integer('signature_id');
            $table->integer('chainmap_id');
            # Indexes
			$table->primary(['wormhole_hash','chainmap_id','signature_id'],'hash_sig');
            $table->index('signature_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('wormhole_signatures');
    }
}
