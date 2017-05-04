
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     * @table sessions
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id',32);
            $table->bigInteger('character_id')->default('0')->unsigned();
            $table->integer('group_id')->unsigned()->default('0');
            $table->integer('chainmap_id')->unsigned()->default('0');
			$table->dateTime('created_at')->nullable()->default(NULL);
			$table->dateTime('updated_at')->nullable()->default(NULL);
            $table->enum('type', ['guest', 'user'])->default('user');
            $table->integer('user_id')->default('0');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('csrf_token', 40)->nullable()->default(NULL);
            # Indexes
			$table->primary('id');
            $table->index('user_id');
            $table->index(['user_id','updated_at']);
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('sessions');
    }
}
