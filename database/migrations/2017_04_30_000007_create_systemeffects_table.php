
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemeffectsTable extends Migration
{
    /**
     * Run the migrations.
     * @table systemeffects
     *
     * @return void
     */
    public function up()
    {
        Schema::create('systemeffects', function (Blueprint $table) {
            $table->integer('id');
            $table->string('effectTitle', 100);
            # Indexes
			$table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('systemeffects');
    }
}
