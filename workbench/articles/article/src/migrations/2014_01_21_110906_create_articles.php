<?php

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
            $table->increments('id');
            $table->integer('app_id', false, true);
            $table->foreign('app_id')->references('id')->on('applications');
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('gallery_id', false, ture);
            $table->foreign('gallery_id')->references('id')->on('galleries');
            $table->string('categories_id', 100);
            $table->string('pre_title',255);
            $table->string('title',255);
            $table->string('picture',100);
            $table->text('teaser');
            $table->text('content');
            $table->string('wrote_by', 40);
            $table->integer('views', false);
            $table->string('tags', 100);
            $table->boolean('status');
            $table->integer('created_at');
			$table->integer('updated_at');
			$table->integer('published_at');
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