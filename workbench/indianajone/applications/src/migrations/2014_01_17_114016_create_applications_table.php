<?php 

use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('applications', function($table){
			$table->increments('id');
			$table->integer('user_id', false, true);
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('name', 100);
			$table->string('description', 255)->nullable();
			$table->string('picture')->nullable();
			$table->string('appkey', 32)->unique();
			$table->integer('created_at');
			$table->integer('updated_at');
		});

		Schema::create('application_meta', function($table){
			$table->increments('id');
			$table->integer('app_id', false, true)->default(0);
			$table->foreign('app_id')->references('id')->on('applications');
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
		Schema::drop('application_meta');
		Schema::drop('applications');
	}

}