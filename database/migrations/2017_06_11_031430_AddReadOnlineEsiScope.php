<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReadOnlineEsiScope extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_ssocharacter', function (Blueprint $table) {
            $table->tinyInteger('scope_esi_location_read_online')->nullable()->default('0');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_ssocharacter', function (Blueprint $table) {
            $table->dropColumn('scope_esi_location_read_online');
		});
    }
}
