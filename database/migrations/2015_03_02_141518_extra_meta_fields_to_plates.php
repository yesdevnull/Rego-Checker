<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtraMetaFieldsToPlates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('plates', function($table) {
            $table->string('status_text', 60);
            $table->tinyInteger('status')->nullable();
            $table->timestamp('last_searched')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('plates', function($table) {
            $table->dropColumn(['status', 'status_text']);
        });
	}

}
