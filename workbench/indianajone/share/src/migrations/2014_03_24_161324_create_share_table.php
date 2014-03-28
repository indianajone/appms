<?php

use Illuminate\Database\Migrations\Migration;

class CreateShareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shares', function($table){
			$table->increments('id');

			// Who is sharing.
			$table->integer('user_id', false, true);
			$table->foreign('user_id')->references('id')->on('users');

			// Where did it get shared.
			$table->string('provider');
			$table->string('content_id');
			
			// When did it get shared or remove.
			$table->integer('created_at', false, true);
			$table->integer('deleted_at', false, true)->nullable();
		});

		Schema::create('shareables', function($table)
		{
			$table->integer('share_id', false, true);

			// What is sharing.
			$table->integer('shareable_id');
			$table->string('shareable_type');

			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_shareables');
	}

}