<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emails', function($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->timestamp('signed_up');
            $table->boolean('enabled');
            $table->string('confirmed', 100);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('emails');
	}

}
