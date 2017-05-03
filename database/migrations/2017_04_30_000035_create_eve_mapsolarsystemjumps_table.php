
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveMapsolarsystemjumpsTable extends Migration
{
    /**
     * Run the migrations.
     * @table eve_mapsolarsystemjumps
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eve_mapsolarsystemjumps', function (Blueprint $table) {
            $table->bigInteger('fromRegionID')->nullable()->default(NULL);
            $table->bigInteger('fromConstellationID')->nullable()->default(NULL);
            $table->bigInteger('fromSolarSystemID');
            $table->bigInteger('toSolarSystemID');
            $table->bigInteger('toConstellationID')->nullable()->default(NULL);
            $table->bigInteger('toRegionID')->nullable()->default(NULL);
            # Indexes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('eve_mapsolarsystemjumps');
    }
}
