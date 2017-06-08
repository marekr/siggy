
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingChargesTable extends Migration
{
    /**
     * Run the migrations.
     * @table billing_charges
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_charges', function (Blueprint $table) {
            $table->increments('chargeID');
            $table->decimal('amount',15,2);
            $table->integer('date');
            $table->integer('groupID');
            $table->integer('memberCount');
            $table->string('message', 255)->default('');
            # Indexes
            $table->index('groupID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('billing_charges');
    }
}
