
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvgroupsTable extends Migration
{
    /**
     * Run the migrations.
     * @table invgroups
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invgroups', function (Blueprint $table) {
            $table->integer('groupID');
            $table->integer('categoryID')->nullable()->default(NULL);
            $table->string('groupName', 200)->nullable()->default(NULL);
            $table->string('description', 6000)->nullable()->default(NULL);
            $table->integer('iconID')->nullable()->default(NULL);
            $table->tinyInteger('useBasePrice')->nullable()->default(NULL);
            $table->tinyInteger('allowManufacture')->nullable()->default(NULL);
            $table->tinyInteger('allowRecycler')->nullable()->default(NULL);
            $table->tinyInteger('anchored')->nullable()->default(NULL);
            $table->tinyInteger('anchorable')->nullable()->default(NULL);
            $table->tinyInteger('fittableNonSingleton')->nullable()->default(NULL);
            $table->tinyInteger('published')->nullable()->default(NULL);
            # Indexes
			$table->primary('groupID');
            $table->index('categoryID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('invgroups');
    }
}
