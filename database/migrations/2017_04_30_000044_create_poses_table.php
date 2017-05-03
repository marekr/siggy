
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosesTable extends Migration
{
    /**
     * Run the migrations.
     * @table poses
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poses', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->string('location_planet', 4);
            $table->string('location_moon', 4);
            $table->string('owner', 255);
            $table->integer('added_date')->default('0');
            $table->integer('system_id')->unsigned()->default('0');
            $table->tinyInteger('online')->default('1');
            $table->mediumInteger('type_id')->default('0');
            $table->enum('size', ['small', 'medium', 'large']);
            $table->text('notes');
            # Indexes
            $table->index(['group_id','system_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('poses');
    }
}
