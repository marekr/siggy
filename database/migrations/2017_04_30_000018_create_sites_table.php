
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     * @table sites
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->enum('type', ['anomaly', 'radar', 'relic', 'data', 'gas', 'ore']);
            $table->string('name', 255);
            $table->text('description')->nullable()->default(NULL);
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

        Schema::drop('sites');
    }
}
