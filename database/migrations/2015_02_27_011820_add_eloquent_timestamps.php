<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEloquentTimestamps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('emails', function($table) {
            $table->dropColumn('signed_up');
        });

        Schema::table('emails', function($table) {
            $table->nullableTimestamps();
        });

        Schema::table('plates', function($table) {
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
		Schema::table('emails', function($table) {
            $table->dropTimestamps();
        });

        Schema::table('emails', function($table) {
            $table->timestamp('signed_up');
        });

        Schema::table('plates', function($table) {
            $table->dropTimestamps();
        });
	}

}
