
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     * @table regions
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->integer('regionID')->unsigned();
            $table->string('regionName', 100)->nullable()->default(NULL);
            $table->integer('factionID')->nullable()->default(NULL);
            # Indexes
			$table->primary('regionID');
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

        Schema::drop('regions');
    }
}
