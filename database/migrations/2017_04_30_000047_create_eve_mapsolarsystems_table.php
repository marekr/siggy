
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveMapsolarsystemsTable extends Migration
{
    /**
     * Run the migrations.
     * @table eve_mapsolarsystems
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eve_mapsolarsystems', function (Blueprint $table) {
            $table->integer('regionID')->nullable()->default(NULL);
            $table->integer('constellationID')->nullable()->default(NULL);
            $table->increments('solarSystemID');
            $table->longText('solarSystemName')->nullable()->default(NULL);
            $table->double('x')->nullable()->default(NULL);
            $table->double('y')->nullable()->default(NULL);
            $table->double('z')->nullable()->default(NULL);
            $table->double('xMin')->nullable()->default(NULL);
            $table->double('xMax')->nullable()->default(NULL);
            $table->double('yMin')->nullable()->default(NULL);
            $table->double('yMax')->nullable()->default(NULL);
            $table->double('zMin')->nullable()->default(NULL);
            $table->double('zMax')->nullable()->default(NULL);
            $table->double('luminosity')->nullable()->default(NULL);
            $table->tinyInteger('border')->nullable()->default(NULL);
            $table->tinyInteger('fringe')->nullable()->default(NULL);
            $table->tinyInteger('corridor')->nullable()->default(NULL);
            $table->tinyInteger('hub')->nullable()->default(NULL);
            $table->tinyInteger('international')->nullable()->default(NULL);
            $table->tinyInteger('regional')->nullable()->default(NULL);
            $table->tinyInteger('constellation')->nullable()->default(NULL);
            $table->double('security')->nullable()->default(NULL);
            $table->integer('factionID')->nullable()->default(NULL);
            $table->double('radius')->nullable()->default(NULL);
            $table->integer('sunTypeID')->nullable()->default(NULL);
            $table->longText('securityClass')->nullable()->default(NULL);
            # Indexes
            $table->index('regionID');
            $table->index('constellationID');
            $table->index('security');
            $table->index([DB::raw('solarSystemName(40)')]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('eve_mapsolarsystems');
    }
}
