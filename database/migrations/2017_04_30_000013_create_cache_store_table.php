
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCacheStoreTable extends Migration
{
    /**
     * Run the migrations.
     * @table cache_store
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache_store', function (Blueprint $table) {
            $table->increments('cacheID');
            $table->string('cacheKey', 60);
            $table->text('cacheValue');
            # Indexes
            $table->unique('cacheKey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('cache_store');
    }
}
