
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     * @table announcements
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('title', 255);
            $table->text('content');
            $table->enum('visibility', ['manage', 'all', 'none'])->default('manage');
            $table->integer('datePublished')->default('0');
            # Indexes
            $table->index('datePublished');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('announcements');
    }
}
