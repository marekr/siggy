<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameChainmap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
		Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

		Schema::table('chainmaps', function (Blueprint $table) {
			$table->renameColumn('chainmap_id','id');
			
			$table->renameColumn('chainmap_name','name');
			$table->renameColumn('chainmap_homesystems','homesystems');
			$table->renameColumn('chainmap_homesystems_ids','homesystems_ids');
			$table->renameColumn('chainmap_skip_purge_home_sigs','skip_purge_home_sigs');

			$table->boolean('default')->default(false);
			$table->boolean('fixed')->default(false);
		});

		DB::statement("UPDATE `chainmaps` SET `default`= 1 WHERE chainmap_type='default'");
		DB::statement("UPDATE `chainmaps` SET `fixed`= 1 WHERE chainmap_type='fixed'");

		Schema::table('chainmaps', function (Blueprint $table) {
			$table->dropColumn('chainmap_type');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('chainmaps', function (Blueprint $table) {
			$table->renameColumn('id','chainmap_id');
			
			$table->renameColumn('name','chainmap_name');
			$table->renameColumn('type','chainmap_type');
			$table->renameColumn('homesystems','chainmap_homesystems');
			$table->renameColumn('homesystems_ids','chainmap_homesystems_ids');
			$table->renameColumn('skip_purge_home_sigs','chainmap_skip_purge_home_sigs');
			
			$table->enum('chainmap_type', ['default', 'fixed', 'user'])->default('fixed');
		});

		DB::statement("UPDATE `chainmaps` SET `chainmap_type`= 'fixed' WHERE fixed=1");
		DB::statement("UPDATE `chainmaps` SET `chainmap_type`= 'default' WHERE default=1");

    }
}
