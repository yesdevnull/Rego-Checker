<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIncrementsIdToPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('email_plate', function($table) {
            $table->increments('id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('email_plate', function($table) {
            $table->dropPrimary('email_plate_id_primary');
            $table->integer('id')->change();
            $table->dropColumn('id');
        });
	}

}
