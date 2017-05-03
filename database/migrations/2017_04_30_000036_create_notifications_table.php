
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     * @table notifications
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->bigInteger('character_id')->default('0')->unsigned();
            $table->string('type', 255);
            $table->text('data')->nullable()->default(NULL);
            $table->integer('created_at')->unsigned();
            # Indexes
            $table->index(['group_id','character_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('notifications');
    }
}
