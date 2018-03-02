<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('billing_payments', function (Blueprint $table) {
			$table->index(['group_id','paid_at']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('billing_payments', function (Blueprint $table) {
			$table->dropIndex(['group_id','paid_at']);
		});
    }
}
