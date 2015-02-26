<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plates', function($table) {
            $table->increments('id');
            $table->enum('state', ['ACT', 'WA', 'NSW', 'VIC', 'TAS', 'SA', 'QLD', 'NT']);
            $table->string('plate', 30)->unique();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('plates');
	}

}
