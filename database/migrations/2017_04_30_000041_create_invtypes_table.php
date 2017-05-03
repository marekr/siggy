
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvtypesTable extends Migration
{
    /**
     * Run the migrations.
     * @table invtypes
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invtypes', function (Blueprint $table) {
            $table->integer('typeID');
            $table->integer('groupID')->nullable()->default(NULL);
            $table->string('typeName', 200)->nullable()->default(NULL);
            $table->string('description', 6000)->nullable()->default(NULL);
            $table->double('mass')->nullable()->default(NULL);
            $table->double('volume')->nullable()->default(NULL);
            $table->double('capacity')->nullable()->default(NULL);
            $table->integer('portionSize')->nullable()->default(NULL);
            $table->tinyInteger('raceID')->nullable()->default(NULL);
            $table->decimal('basePrice', 19, 4)->nullable()->default(NULL);
            $table->tinyInteger('published')->nullable()->default(NULL);
            $table->integer('marketGroupID')->nullable()->default(NULL);
            $table->double('chanceOfDuplicating')->nullable()->default(NULL);
            # Indexes
			$table->primary('typeID');
            $table->index('groupID');
            $table->index('typeName');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('invtypes');
    }
}
