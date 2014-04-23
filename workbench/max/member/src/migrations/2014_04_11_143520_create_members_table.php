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
    
            $table->string('username', 40)->unique();
            $table->string('password', 100);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('displayname', 40)->nullable();
            $table->boolean('verified')->nullable()->default(0);
            $table->string('email', 40);
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->integer('deleted_at')->nullable();
            $table->integer('last_seen')->nullable();
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
        Schema::drop('member_meta');
		Schema::drop('members');
	}

}