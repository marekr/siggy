
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersGroupAclTable extends Migration
{
    /**
     * Run the migrations.
     * @table users_group_acl
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_group_acl', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('group_id');
            $table->tinyInteger('can_view_logs')->default('0');
            $table->tinyInteger('can_manage_group_members')->default('0');
            $table->tinyInteger('can_manage_settings')->default('0');
            $table->tinyInteger('can_view_financial')->default('0');
            $table->tinyInteger('can_manage_access')->default('0');
            # Indexes
            $table->unique(['user_id','group_id']);
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('users_group_acl');
    }
}
