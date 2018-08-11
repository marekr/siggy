<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Siggy\User;
use Siggy\SiggyUserProvider;

class ConvertPasswords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('users', function (Blueprint $table) {
			$table->string('password')->change();
		});

		User::chunk(100, function($users){
			foreach ($users as $user) {
				$user->update([
					'password' => SiggyUserProvider::newHash($user->password)
				]);
			}
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        throw new Exception("NO TURNING BACK ON PASSWORD HASHES");
    }
}
