<?php

use Illuminate\Database\Migrations\Migration;

class CreateMembers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('members', function($table) {
                    $table->increments('member_id');
                    $table->integer('app_id', false);
                    $table->integer('parent_id', false);
                    $table->string('fbid', 40);
                    $table->text('fbtoken');
                    $table->string('username',40);
                    $table->string('password',40);
                    $table->string('title',10);
                    $table->string('first_name',100);
                    $table->string('last_name',100);
                    $table->string('other_name',100);
                    $table->string('phone',40);
                    $table->string('mobile',10);
                    $table->integer('otp', false);
                    $table->boolean('verified');
                    $table->string('email',40);
                    $table->string('address',255);
                    $table->string('gender',10);
                    $table->timestamp('birthday');
                    $table->string('description',255);
                    $table->string('type',10);
                    $table->timestamps();
                    $table->timestamp('last_seen');
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
		Schema::drop('members');
	}

}