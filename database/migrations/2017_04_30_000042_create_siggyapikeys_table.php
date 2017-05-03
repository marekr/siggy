
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiggyapikeysTable extends Migration
{
    /**
     * Run the migrations.
     * @table siggyapikeys
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siggyapikeys', function (Blueprint $table) {
            $table->increments('keyID');
            $table->string('keyName', 100)->default('');
            $table->string('keyCode', 255)->default('');
            $table->integer('groupID')->default('0');
            $table->integer('subGroupID')->default('-1');
            # Indexes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('siggyapikeys');
    }
}
