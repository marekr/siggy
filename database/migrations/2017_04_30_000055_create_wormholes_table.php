
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWormholesTable extends Migration
{
    /**
     * Run the migrations.
     * @table wormholes
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wormholes', function (Blueprint $table) {
            $table->string('hash', 33);
            $table->integer('to_system_id')->unsigned()->default('0');
            $table->integer('from_system_id')->unsigned()->default('0');
            $table->integer('group_id')->unsigned()->default('0');
            $table->integer('last_jump')->unsigned()->default('0');
            $table->integer('chainmap_id')->unsigned()->default('0');
            $table->tinyInteger('eol')->default('0');
            $table->tinyInteger('mass')->default('0');
            $table->integer('eol_date_set')->unsigned()->default('0');
            $table->tinyInteger('frigate_sized')->default('0');
            $table->integer('wh_type_id')->default('0');
            # Indexes
			$table->primary(['hash','group_id','chainmap_id']);
            $table->index(['group_id','chainmap_id']);
            $table->index(['to_system_id','group_id','chainmap_id']);
            $table->index(['from_system_id','group_id','chainmap_id']);
            $table->index(['group_id','chainmap_id','to_system_id','from_system_id']);
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

        Schema::drop('wormholes');
    }
}
