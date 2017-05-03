
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivesystemsTable extends Migration
{
    /**
     * Run the migrations.
     * @table activesystems
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activesystems', function (Blueprint $table) {
            $table->integer('systemID')->unsigned();
            $table->integer('groupID')->unsigned();
            $table->integer('lastUpdate')->unsigned()->default('0');
            $table->string('displayName', 100)->nullable();
            $table->integer('lastActive')->unsigned()->default('0');
            $table->tinyInteger('inUse')->default('0');
            $table->integer('chainmap_id')->unsigned();
            $table->mediumInteger('x')->default('40');
            $table->mediumInteger('y')->default('40');
            $table->tinyInteger('activity')->default('0');
            $table->tinyInteger('rally')->default('0');
            $table->tinyInteger('hazard')->default('0');
            # Indexes
            $table->primary(['systemID','groupID','chainmap_id']);
            $table->index(['groupID','chainmap_id','inUse','lastActive','activity'],'query');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('activesystems');
    }
}
