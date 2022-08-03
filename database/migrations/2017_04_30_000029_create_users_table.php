
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 100);
            $table->string('username', 100);
            $table->char('password', 64);
            $table->integer('groupID')->unsigned()->default('0');
            $table->integer('logins')->unsigned()->default('0');
            $table->integer('last_login')->unsigned()->nullable()->default('0');
            $table->char('reset_token', 64)->default('');
            $table->integer('last_failed_login')->default('0');
            $table->integer('failed_login_count')->default('0');
            $table->tinyInteger('active')->default('0');
            $table->string('ip_address', 45)->nullable();
            $table->tinyInteger('admin')->default('0');
            $table->bigInteger('char_id')->default('0')->unsigned();
			$table->dateTime('created_at')->nullable()->default(NULL);
			$table->dateTime('updated_at')->nullable()->default(NULL);
            $table->integer('theme_id')->default('0');
            $table->tinyInteger('combine_scan_intel')->default('0');
            $table->string('language', 5)->default('en');
            $table->string('default_activity', 16)->nullable()->default(NULL);
            # Indexes
            $table->unique('username');
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('users');
    }
}
