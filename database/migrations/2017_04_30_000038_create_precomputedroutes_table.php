
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrecomputedroutesTable extends Migration
{
    /**
     * Run the migrations.
     * @table precomputedroutes
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precomputedroutes', function (Blueprint $table) {
            $table->integer('origin_system')->default('0');
            $table->integer('destination_system')->default('0');
            $table->mediumInteger('num_jumps')->default('-1');
            $table->text('route');
            $table->enum('type', ['shortest', 'safest'])->default('shortest');
            # Indexes
            $table->unique(['origin_system','destination_system','type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('precomputedroutes');
    }
}
