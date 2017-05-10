<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewApiKeySystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('apikeys', function (Blueprint $table) {
			$table->string('id',16);
			$table->string('secret',32);
			$table->integer('group_id')->unsigned();
			# Indexes
			$table->primary('id');
			$table->index('group_id');
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
        Schema::drop('apikeys');
    }
}
