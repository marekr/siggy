
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDscanRecordsTable extends Migration
{
    /**
     * Run the migrations.
     * @table dscan_records
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dscan_records', function (Blueprint $table) {
            $table->string('dscan_id', 14);
            $table->integer('type_id');
            $table->string('record_name', 255)->default('');
            $table->string('item_distance', 50)->default('');
            # Indexes
            $table->index(['dscan_id','type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('dscan_records');
    }
}
