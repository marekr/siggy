
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupmembersTable extends Migration
{
    /**
     * Run the migrations.
     * @table groupmembers
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupmembers', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('eveID')->unsigned();
            $table->enum('memberType', ['char', 'corp', 'alliance']);
            $table->integer('groupID')->unsigned();
            $table->string('accessName', 100);
            # Indexes
            $table->unique(['eveID','memberType','groupID']);
            $table->index('groupID');
            $table->index('eveID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('groupmembers');
    }
}
