<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApplications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('applications', function(Blueprint $table){
			$table->increments('id');
			$table->integer('user_id', false, true);
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('name', 100);
			$table->string('description', 255);
			$table->string('picture');
			$table->string('appkey', 100);
			$table->integer('create_at');
			$table->integer('update_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('applications');
	}

}