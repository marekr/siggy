
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipsTable extends Migration
{
    /**
     * Run the migrations.
     * @table ships
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ships', function (Blueprint $table) {
            $table->bigInteger('shipID')->unsigned();
            $table->string('shipName', 255);
            $table->bigInteger('mass')->unsigned();
            $table->string('shipClass', 255);
            # Indexes
			$table->primary('shipID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('ships');
    }
}
