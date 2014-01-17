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
                $table->increments('like_id');
                $table->integer('app_id', false);
                $table->integer('member_id', false);
                $table->string('content_id', 100);
                $table->string('type',10);
                $table->boolean('status');
                $table->timestamps();
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