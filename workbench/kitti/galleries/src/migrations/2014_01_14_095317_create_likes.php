<?php

use Illuminate\Database\Migrations\Migration;

class CreateLikes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('likes', function($table) {
                $table->increments('id');
                $table->integer('app_id', false)->unsigned();
                $table->foreign('app_id')->references('id')->on('applications');
                $table->integer('member_id', false)->unsigned();
                $table->foreign('member_id')->references('id')->on('members');
                $table->string('content_id', 100);
                $table->string('type',10);
                $table->integer('created_at', false);
            	$table->integer('updated_at', false);
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
            Schema::drop('likes');
	}

}