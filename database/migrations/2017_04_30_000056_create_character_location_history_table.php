
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterLocationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     * @table character_location_history
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_location_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('character_id')->unsigned();
            $table->bigInteger('current_system_id');
            $table->bigInteger('previous_system_id');
            $table->dateTime('changed_at');
            $table->bigInteger('ship_id')->default('0');
            # Indexes
            $table->index('current_system_id');
            $table->index('previous_system_id');
            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('character_location_history');
    }
}
