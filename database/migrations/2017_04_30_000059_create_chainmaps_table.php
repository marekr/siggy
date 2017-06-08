
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChainmapsTable extends Migration
{
    /**
     * Run the migrations.
     * @table chainmaps
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chainmaps', function (Blueprint $table) {
            $table->increments('chainmap_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->string('chainmap_name', 60);
            $table->text('chainmap_homesystems');
            $table->text('chainmap_homesystems_ids');
            $table->tinyInteger('chainmap_skip_purge_home_sigs')->default('0');
            $table->enum('chainmap_type', ['default', 'fixed', 'user'])->default('fixed');
            # Indexes
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('chainmaps');
    }
}
