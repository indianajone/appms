<?php

use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('members', function($table)
        {
            $table->increments('id');
            $table->integer('app_id')->unsigned();
            $table->foreign('app_id')->references('id')->on('applications');
            // $table->string('fbid', 40)->nullable();
            // $table->text('fbtoken')->nullable();
            $table->string('username', 40)->unique();
            $table->string('password', 100);
            // $table->string('title', 10)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('displayname', 40)->nullable();
            // $table->string('phone', 40)->nullable();
            // $table->string('mobile', 10)->nullable();
            $table->boolean('verified')->nullable()->default(0);
            $table->string('email', 40);
            // $table->string('address', 255)->nullable();
            // $table->string('gender', 10)->nullable();
            // $table->integer('birthday')->nullable();
            // $table->string('description', 255)->nullable();
            // $table->string('type', 10);
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->integer('deleted_at')->nullable();
            $table->integer('last_seen');
        });

        Schema::create('member_meta', function($table)
        {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->foreign('member_id')->references('id')->on('members');
            $table->string('meta_key')->default('null')->nullable()->index();
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
		Schema::drop('members');
	}

}