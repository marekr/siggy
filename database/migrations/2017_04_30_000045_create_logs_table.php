
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     * @table logs
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('logID');
            $table->integer('groupID')->unsigned();
            $table->integer('entryTime')->unsigned()->default('0');
            $table->enum('type', ['delsig', 'editsig', 'general', 'delwh', 'delwhs', 'editmap', 'editsystem', 'addwh', 'delpos', 'editpos'])->default('general');
            $table->text('message');
            # Indexes
            $table->index(['logID','groupID','type']);
            $table->index('groupID');
            $table->index(['groupID','logID']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('logs');
    }
}
