
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterLocationTable extends Migration
{
    /**
     * Run the migrations.
     * @table character_location
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_location', function (Blueprint $table) {
            $table->bigInteger('character_id')->unsigned();
            $table->bigInteger('system_id')->nullable()->default(NULL);
            $table->dateTime('updated_at')->nullable()->default(NULL);
            $table->bigInteger('ship_id')->nullable()->default('0');
            # Indexes
			$table->primary('character_id');
            $table->index('system_id');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('character_location');
    }
}
