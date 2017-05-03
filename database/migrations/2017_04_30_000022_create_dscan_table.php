
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDscanTable extends Migration
{
    /**
     * Run the migrations.
     * @table dscan
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dscan', function (Blueprint $table) {
            $table->string('dscan_id',14);
            $table->integer('group_id')->unsigned();
            $table->integer('system_id')->default('0');
            $table->string('dscan_title', 255)->default('255');
            $table->integer('dscan_date');
            $table->string('dscan_added_by', 255)->default('');
            # Indexes
			$table->primary('dscan_id');
            $table->unique(['dscan_id','group_id','system_id']);
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

        Schema::drop('dscan');
    }
}
