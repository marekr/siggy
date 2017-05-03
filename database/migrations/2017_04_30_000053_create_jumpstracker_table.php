
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJumpstrackerTable extends Migration
{
    /**
     * Run the migrations.
     * @table jumpstracker
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jumpstracker', function (Blueprint $table) {
            $table->integer('systemID')->unsigned()->default('0');
            $table->integer('groupID')->unsigned()->default('0');
            $table->integer('hourStamp')->unsigned()->default('0');
            $table->smallInteger('jumps')->default('0')->unsigned();
            # Indexes
			$table->primary(['systemID','groupID','hourStamp']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('jumpstracker');
    }
}
