
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupCharacterBlacklistTable extends Migration
{
    /**
     * Run the migrations.
     * @table group_character_blacklist
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_character_blacklist', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->string('character_name', 255)->default('');
            $table->bigInteger('character_id')->unsigned();
            $table->text('reason');
            $table->dateTime('created_at')->nullable()->default(NULL);
            # Indexes
            $table->unique(['group_id','character_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('group_character_blacklist');
    }
}
