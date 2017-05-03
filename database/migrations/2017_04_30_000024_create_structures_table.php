
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructuresTable extends Migration
{
    /**
     * Run the migrations.
     * @table structures
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->bigInteger('system_id')->unsigned();
            $table->bigInteger('creator_character_id')->unsigned();
            $table->integer('type_id');
            $table->text('notes')->nullable()->default(NULL);
            $table->bigInteger('corporation_id')->nullable()->default(NULL)->unsigned();
            $table->string('corporation_name', 255)->nullable()->default(NULL);
            # Indexes
            $table->index(['group_id','system_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('structures');
    }
}
