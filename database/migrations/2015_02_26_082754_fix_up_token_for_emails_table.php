<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixUpTokenForEmailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('emails', function($table) {
            $table->dropColumn('confirmed');
        });

        Schema::table('emails', function($table) {
            $table->string('token', 100);
            $table->boolean('confirmed')->default(0);
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
            $table->dropColumn(['token', 'confirmed']);
        });

        Schema::table('emails', function($table) {
            $table->string('confirmed', 100);
        });
	}

}
