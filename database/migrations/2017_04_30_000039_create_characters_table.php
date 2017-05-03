
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     * @table characters
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->string('name', 255);
            $table->bigInteger('corporation_id')->nullable()->default(NULL)->unsigned();
            $table->dateTime('location_processed_at')->nullable()->default(NULL);
            $table->dateTime('last_sync_successful_at')->nullable()->default(NULL);
            $table->dateTime('last_sync_attempt_at')->nullable()->default(NULL);
            # Indexes
			$table->primary('id');
            $table->index('corporation_id');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('characters');
    }
}
