
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticmapTable extends Migration
{
    /**
     * Run the migrations.
     * @table staticmap
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staticmap', function (Blueprint $table) {
            $table->integer('system_id');
            $table->integer('static_id');
            # Indexes
            $table->unique(['system_id','static_id']);
            $table->index('system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('staticmap');
    }
}
