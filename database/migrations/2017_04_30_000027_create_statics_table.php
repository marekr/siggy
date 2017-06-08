
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticsTable extends Migration
{
    /**
     * Run the migrations.
     * @table statics
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statics', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('name', 5);
            $table->integer('dest_class')->default('0');
            $table->bigInteger('mass');
            $table->bigInteger('jump_mass');
            $table->integer('lifetime');
            $table->integer('regen');
            $table->string('sig_size', 255);
            $table->text('description');
            # Indexes
			$table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('statics');
    }
}
