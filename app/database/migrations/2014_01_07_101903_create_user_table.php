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
<<<<<<< HEAD
			$table->integer('parent_id')->unsigned()->default(0);
=======
			$table->integer('parent_id')->unsigned()->default(null);
>>>>>>> best
			$table->string('first_name', 100);
			$table->string('last_name', 100);
			$table->string('gender', 10)->nullable();
			$table->string('email', 100)->unique();
			$table->string('username', 40)->unique();
			$table->string('password', 100);
			$table->integer('birthday')->nullable();
			$table->integer('created_at');
			$table->integer('updated_at');
			$table->integer('last_seen');
		});
<<<<<<< HEAD

		// // Creates password reminders table
  //       Schema::create('password_reminders', function($table)
  //       {
  //           $table->string('email');
  //           $table->string('token');
  //           $table->integer('created_at');
  //       });
	}


=======
	}

>>>>>>> best
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('password_reminders');
		Schema::drop('user');
	}

}
