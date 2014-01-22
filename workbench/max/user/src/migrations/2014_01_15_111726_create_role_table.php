<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('roles', function(Blueprint $table)
            {
                $table->increments('id')->unique();
                $table->string('name', 100);
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
            
            Schema::create('user_roles', function(Blueprint $table)
            {
                $table->increments('id')->unique();
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->integer('role_id')->unsigned();
                $table->foreign('role_id')->references('id')->on('roles');
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('roles', function(Blueprint $table)
		{
			//
		});
	}

}