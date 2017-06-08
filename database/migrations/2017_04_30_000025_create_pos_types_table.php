
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosTypesTable extends Migration
{
    /**
     * Run the migrations.
     * @table pos_types
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->default('');
            # Indexes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('pos_types');
    }
}
