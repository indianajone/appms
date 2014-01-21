<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('users', function(Blueprint $table)
            {
                $table->increments('id')->unique();
                $table->integer('parent_id')->unsigned();
                $table->string('username', 40)->unique();
                $table->string('password', 100);
                $table->string('first_name', 40);
                $table->string('last_name', 40);
                $table->string('email', 40)->unique();
                $table->string('gender', 10)->nullable();
                $table->integer('birthday')->nullable();
                $table->timestamps();
                $table->integer('last_seen');
            });
            
            Schema::create('plugin_user', function(Blueprint $table)
            {
                $table->increments('id')->unique();
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->boolean('status')->default(0);
                $table->timestamps('updated_at ');
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			//
		});
	}

}