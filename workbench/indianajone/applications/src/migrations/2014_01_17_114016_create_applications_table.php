<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('applications', function(Blueprint $table){
			$table->increments('id');
			$table->integer('user_id', false)->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('name', 100);
			$table->string('description', 255)->nullable();
			$table->string('picture')->nullable();
			$table->string('appkey', 32)->unique();
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
		Schema::drop('applications');
	}

}