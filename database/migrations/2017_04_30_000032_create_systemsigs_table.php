
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemsigsTable extends Migration
{
    /**
     * Run the migrations.
     * @table systemsigs
     *
     * @return void
     */
    public function up()
    {
        Schema::create('systemsigs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('systemID');
            $table->string('sig', 3);
            $table->enum('type', ['wh', 'ladar', 'grav', 'mag', 'radar', 'none', 'combat', 'ore', 'gas', 'relic', 'data', 'anomaly'])->default('none');
            $table->integer('siteID')->default('0');
            $table->string('description', 512)->default('');
            $table->string('sigSize', 5)->default('');
            $table->integer('groupID')->default('0');
            $table->string('creator', 255)->default('');
            $table->string('lastUpdater', 255)->default('');
            # Indexes
            $table->index(['systemID','groupID']);
            $table->index(['groupID','type']);
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

        Schema::drop('systemsigs');
    }
}
