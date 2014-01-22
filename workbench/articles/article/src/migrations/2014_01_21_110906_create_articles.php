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
                    $table->increments('article_id');
                    $table->integer('app_id', false);
                    $table->integer('gallery_id', false);
                    $table->integer('user_id', false);
                    $table->string('categories_id', 100);
                    $table->string('pre_title',255);
                    $table->string('title',255);
                    $table->string('picture',100);
                    $table->text('teaser');
                    $table->text('content');
                    $table->string('wrote_by', 40);
                    $table->timestamps();
                    $table->timestamp('publish_at');
                    $table->integer('views', false);
                    $table->string('tags', 100);
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