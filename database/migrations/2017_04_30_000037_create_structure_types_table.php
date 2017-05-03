
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructureTypesTable extends Migration
{
    /**
     * Run the migrations.
     * @table structure_types
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structure_types', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->string('name', 255);
            $table->integer('weekly_vulnerability')->nullable()->default(NULL);
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

        Schema::drop('structure_types');
    }
}
