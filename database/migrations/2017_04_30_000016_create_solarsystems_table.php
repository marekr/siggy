
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolarsystemsTable extends Migration
{
    /**
     * Run the migrations.
     * @table solarsystems
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solarsystems', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('name', 100);
            $table->integer('belts')->default('0');
            $table->integer('planets')->default('0');
            $table->integer('moons')->default('0');
            $table->tinyInteger('sysClass')->default('9');
            $table->double('truesec')->default('0');
            $table->decimal('sec', 2, 1)->default('0.0');
            $table->integer('effect');
            $table->decimal('radius', 7, 2);
            $table->integer('region')->unsigned();
            $table->integer('constellation');
            # Indexes
			$table->primary('id');
            $table->index('name');
            $table->index('constellation');
            $table->index('region');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('solarsystems');
    }
}
