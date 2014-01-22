<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articles', function($table) {
			// primary key
            $table->increments('id');

            //foreign key
            $table->integer('app_id', false)->unsigned();
            $table->foreign('app_id')->references('id')->on('applications');
            $table->integer('gallery_id', false)->unsigned()->nullable();
            $table->foreign('gallery_id')->references('id')->on('galleries');
            $table->integer('user_id', false)->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            // index
            $table->string('categories_id', 100);

            // others
            $table->string('pre_title',255)->nullable();
            $table->string('title',255);
            $table->string('picture',100)->nullable();
            $table->text('teaser')->nullable();
            $table->text('content');
            $table->string('wrote_by', 40);
      		$table->integer('created_at');
      		$table->integer('updated_at');
            $table->integer('publish_at');
            $table->integer('views', false)->unsigned();
            $table->string('tags', 100)->nullable();
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
		Schema::drop('articles');
	}

}