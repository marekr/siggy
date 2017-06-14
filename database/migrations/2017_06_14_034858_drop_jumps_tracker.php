<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropJumpsTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('jumpstracker');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('jumpstracker', function (Blueprint $table) {
            $table->integer('systemID')->unsigned()->default('0');
            $table->integer('groupID')->unsigned()->default('0');
            $table->integer('hourStamp')->unsigned()->default('0');
            $table->smallInteger('jumps')->default('0')->unsigned();
            # Indexes
			$table->primary(['systemID','groupID','hourStamp']);
        });
    }
}
