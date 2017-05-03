
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifiersTable extends Migration
{
    /**
     * Run the migrations.
     * @table notifiers
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifiers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->bigInteger('character_id')->unsigned();
            $table->string('type', 255);
            $table->enum('scope', ['personal', 'group']);
            $table->text('data');
            # Indexes
            $table->index(['group_id','character_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('notifiers');
    }
}
