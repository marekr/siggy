
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChainmapsAccessTable extends Migration
{
    /**
     * Run the migrations.
     * @table chainmaps_access
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chainmaps_access', function (Blueprint $table) {
            $table->integer('chainmap_id');
            $table->integer('group_id');
            $table->integer('groupmember_id');
            $table->tinyInteger('view')->default('1');
            $table->tinyInteger('edit')->default('1');
            # Indexes
			$table->primary(['chainmap_id','group_id','groupmember_id']);
            $table->index('groupmember_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('chainmaps_access');
    }
}
