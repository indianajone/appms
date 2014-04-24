<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) {
			$table->increments('id');
			$table->integer('parent_id')->unsigned()->nullable();
			$table->foreign('parent_id')->references('id')->on('users');
			$table->string('display_name', 50)->nullable();
			$table->string('first_name', 100);
			$table->string('last_name', 100);
			$table->string('email', 100)->unique();
			$table->string('username', 60)->unique();
			$table->string('password', 64);
			$table->integer('created_at');
			$table->integer('updated_at');
			$table->integer('deleted_at')->nullable();
		});

		Schema::create('user_meta', function($table){
			$table->increments('id');
			$table->integer('user_id', false, true)->default(0);
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('meta_key')->nullable()->index();
			$table->text('meta_value')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_meta');
		Schema::drop('users');
	}

}
