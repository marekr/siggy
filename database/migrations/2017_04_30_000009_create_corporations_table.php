
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationsTable extends Migration
{
    /**
     * Run the migrations.
     * @table corporations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporations', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->string('name', 255)->default('');
            $table->string('ticker', 10)->default('');
            $table->text('description')->nullable()->default(NULL);
            $table->integer('member_count')->default('0');
			$table->dateTime('created_at')->nullable()->default(NULL);
			$table->dateTime('updated_at')->nullable()->default(NULL);
            $table->dateTime('last_sync_successful_at')->nullable()->default(NULL);
            $table->dateTime('last_sync_attempt_at')->nullable()->default(NULL);
            # Indexes
			$table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('corporations');
    }
}
