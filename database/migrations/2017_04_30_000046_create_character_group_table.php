
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterGroupTable extends Migration
{
    /**
     * Run the migrations.
     * @table character_group
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_group', function (Blueprint $table) {
            $table->integer('character_id');
            $table->integer('group_id');
            $table->integer('last_notification_read')->unsigned()->default('0');
            $table->dateTime('last_group_access_at')->nullable()->default(NULL);
            # Indexes
			$table->primary(['character_id','group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('character_group');
    }
}
