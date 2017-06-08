
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterSettingsTable extends Migration
{
    /**
     * Run the migrations.
     * @table character_settings
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_settings', function (Blueprint $table) {
            $table->bigInteger('char_id')->unsigned();
            $table->integer('theme_id');
            $table->tinyInteger('combine_scan_intel')->default('0');
            $table->decimal('zoom', 5, 2)->default('1.00');
            $table->string('language', 5)->default('en');
            $table->string('default_activity', 16)->nullable()->default(NULL);
            # Indexes
			$table->primary('char_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('character_settings');
    }
}
