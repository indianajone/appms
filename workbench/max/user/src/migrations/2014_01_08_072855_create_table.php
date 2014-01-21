<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('users', function(Blueprint $table)
            {
                $table->increments('user_id');
                $table->integer('parent_id')->unsigned();
                $table->string('username', 40);
                $table->string('password', 40);
                $table->string('first_name', 40);
                $table->string('last_name', 40);
                $table->string('email', 40);
                $table->string('gender', 40);
                $table->dateTime('birthday');
                $table->dateTime('create_date');
                $table->dateTime('update_date');
                $table->dateTime('last_seen');
            });
            
            Schema::create('applications', function(Blueprint $table)
            {
                $table->increments('app_id');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('user_id')->on('users');
                $table->string('name', 40);
                $table->string('description', 100);
                $table->string('picture', 100);
                $table->string('appkey', 50);
                $table->dateTime('create_date');
                $table->dateTime('update_date');
            });
            
            Schema::create('plugin_user', function(Blueprint $table)
            {
                $table->increments('plugin_id');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('user_id')->on('users');
                $table->boolean('status');
                $table->dateTime('update_date');
            });
            
            Schema::create('roles', function(Blueprint $table)
            {
                $table->increments('role_id');
                $table->string('name', 100);
                $table->string('description', 255);
                $table->dateTime('create_date');
                $table->dateTime('update_date');
            });
            
            Schema::create('user_roles', function(Blueprint $table)
            {
                $table->increments('assigned_role_id');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('user_id')->on('users');
                $table->integer('role_id')->unsigned();
                $table->foreign('role_id')->references('role_id')->on('roles');
            });
            
            Schema::create('application_plugin', function(Blueprint $table)
            {
                $table->increments('plugin_id');
                $table->integer('app_id')->unsigned();
                $table->foreign('app_id')->references('app_id')->on('applications');
                $table->boolean('status');
                $table->dateTime('update_date');
            });
            
            Schema::create('members', function(Blueprint $table)
            {
                $table->increments('member_id');
                $table->integer('app_id')->unsigned();
                $table->foreign('app_id')->references('app_id')->on('applications');
                $table->integer('parent_id');
                $table->string('fbid', 40);
                $table->text('fbtoken');
                $table->string('username', 40);
                $table->string('password', 40);
                $table->string('title', 10);
                $table->string('first_name', 100);
                $table->string('last_name', 100);
                $table->string('other_name', 40);
                $table->string('phone', 40);
                $table->string('mobile', 10);
                $table->integer('otp');
                $table->boolean('verified');
                $table->string('email', 40);
                $table->string('address', 255);
                $table->string('gender', 10);
                $table->dateTime('birthday');
                $table->string('description', 255);
                $table->string('type', 10);
                $table->dateTime('create_date');
                $table->dateTime('update_date');
                $table->dateTime('last_seen');
                $table->boolean('status');
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}