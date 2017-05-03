
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructureVulnerabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     * @table structure_vulnerabilities
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structure_vulnerabilities', function (Blueprint $table) {
            $table->integer('id');
            $table->tinyInteger('day')->unsigned();
            $table->tinyInteger('hour')->unsigned();
            # Indexes
			$table->primary(['id','day','hour']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('structure_vulnerabilities');
    }
}
