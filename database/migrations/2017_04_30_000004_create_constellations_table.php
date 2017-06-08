
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConstellationsTable extends Migration
{
    /**
     * Run the migrations.
     * @table constellations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('constellations', function (Blueprint $table) {
            $table->integer('regionID')->nullable()->default(NULL);
            $table->integer('constellationID')->unsigned();
            $table->string('constellationName', 100)->nullable()->default(NULL);
            $table->integer('factionID')->nullable()->default(NULL);
            $table->double('radius')->nullable()->default(NULL);
            # Indexes
			$table->primary('constellationID');
            $table->unique(['constellationID','regionID']);
            $table->index('regionID');
            $table->index('factionID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('constellations');
    }
}
