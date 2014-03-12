<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('parent_id')->unsigned()->nullable();
			$table->foreign('parent_id')->references('id')->on('users');
			$table->string('first_name', 100);
			$table->string('last_name', 100);
			$table->string('gender', 10)->nullable();
			$table->string('email', 100)->unique();
			$table->string('username', 40)->unique();
			$table->string('password', 100);
			$table->integer('birthday')->nullable();
			$table->integer('last_seen');
			$table->integer('created_at');
			$table->integer('updated_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user');
	}

}
