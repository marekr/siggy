<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupPasswordsTable extends Migration
{
    /**
     * Run the migrations.
     * @table user_group_passwords
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group_passwords', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('group_id');
            $table->string('group_password', 255);
            # Indexes
            $table->primary(['user_id','group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('user_group_passwords');
    }
}
