
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     * @table groups
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('ticker', 10);
            $table->integer('last_update')->default('0');
            $table->text('notes')->nullable()->default(NULL);
            $table->tinyInteger('password_required')->default('0');
            $table->string('password_salt', 10)->default('');
            $table->string('password', 40)->default('');
            $table->tinyInteger('skip_purge_home_sigs')->default('0');
            $table->tinyInteger('stats_enabled')->default('0');
            $table->tinyInteger('record_jumps')->default('1');
            $table->tinyInteger('show_sig_size_col')->default('0');
            $table->double('stats_sig_add_points')->default('1');
            $table->double('stats_sig_update_points')->default('1');
            $table->double('stats_wh_map_points')->default('1');
            $table->double('stats_pos_add_points')->default('1');
            $table->double('stats_pos_update_points')->default('1');
            $table->tinyInteger('jump_log_enabled')->default('1');
            $table->tinyInteger('jump_log_record_names')->default('1');
            $table->tinyInteger('jump_log_record_time')->default('1');
            $table->string('billing_contact', 255)->nullable();
            $table->decimal('isk_balance',15,2)->default('0');
            $table->string('payment_code', 14)->default('');
            $table->tinyInteger('billable')->default('1');
            $table->tinyInteger('always_broadcast')->default('0');
            $table->tinyInteger('jump_log_display_ship_type')->default('1');
            $table->tinyInteger('chain_map_show_actives_ships')->default('1');
            $table->tinyInteger('allow_map_height_expand')->default('1');
            $table->tinyInteger('eet_beta')->default('0');
            $table->tinyInteger('chainmap_always_show_class')->default('0');
            $table->tinyInteger('chainmap_max_characters_shown')->default('5');
            $table->string('default_activity', 16)->nullable()->default(NULL);
            $table->dateTime('created_at')->nullable()->default(NULL);
            $table->dateTime('updated_at')->nullable()->default(NULL);
            $table->dateTime('last_billing_charge_at')->nullable()->default(NULL);
            # Indexes
            $table->index('payment_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {

        Schema::drop('groups');
    }
}
