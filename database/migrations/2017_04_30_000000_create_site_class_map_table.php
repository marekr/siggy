
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteClassMapTable extends Migration
{
    /**
     * Run the migrations.
     * @table site_class_map
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_class_map', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('site_id');
            $table->integer('system_class');
            $table->integer('position');
            # Indexes
            $table->unique(['site_id','system_class']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
        Schema::drop('site_class_map');
    }
}
