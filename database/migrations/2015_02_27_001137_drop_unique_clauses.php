<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniqueClauses extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('emails', function($table) {
            $table->dropUnique('emails_email_unique');
        });

        Schema::table('plates', function($table) {
            $table->dropUnique('plates_plate_unique');
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
            $table->unique('email');
        });

        Schema::table('plates', function($table) {
            $table->unique('plate');
        });
	}

}
