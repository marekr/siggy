
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSsocharacterTable extends Migration
{
    /**
     * Run the migrations.
     * @table user_ssocharacter
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ssocharacter', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('character_owner_hash', 100)->nullable()->default(NULL);
            $table->bigInteger('character_id')->nullable()->default(NULL);
            $table->string('access_token', 100)->nullable()->default(NULL);
            $table->dateTime('access_token_expiration')->nullable()->default(NULL);
            $table->string('refresh_token', 100)->nullable()->default(NULL);
			$table->dateTime('created_at')->nullable()->default(NULL);
			$table->dateTime('updated_at')->nullable()->default(NULL);
            $table->tinyInteger('valid')->nullable()->default('0');
            $table->tinyInteger('always_track_location')->nullable()->default('1');
            $table->tinyInteger('scope_character_location_read')->nullable()->default('0');
            $table->tinyInteger('scope_character_navigation_write')->nullable()->default('0');
            $table->tinyInteger('scope_esi_location_read_location')->nullable()->default('0');
            $table->tinyInteger('scope_esi_location_read_ship_type')->nullable()->default('0');
            $table->tinyInteger('scope_esi_ui_write_waypoint')->nullable()->default('0');
            $table->tinyInteger('scope_esi_ui_open_window')->nullable()->default('0');
            # Indexes
            $table->unique('character_owner_hash');
            $table->index(['user_id','character_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('user_ssocharacter');
    }
}
