<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMissingChildTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('missingchilds', function(Blueprint $table)
        {
        	$table->increments('id');
            $table->integer('app_id', false)->unsigned();
            $table->foreign('app_id')->references('id')->on('applications');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('article_id')->unsigned()->nullable();
            $table->foreign('article_id')->references('id')->on('articles');
            $table->integer('gallery_id')->unsigned()->nullable();
            $table->foreign('gallery_id')->references('id')->on('galleries');

            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('nickname', 40)->nullable();
            $table->text('description');
            $table->integer('age')->nullable();
            $table->integer('lost_age')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('picture', 100)->nullable();
            $table->integer('birth_date')->nullable();

            $table->text('place_of_missing')->nullable();
            $table->string('latitude',25)->nullable();
            $table->string('longitude',25)->nullable();
            $table->integer('missing_at');

            $table->string('reported_place');
            $table->integer('reported_at');

            $table->boolean('notify')->default(0);
            $table->string('notify_text')->nullable();

            $table->text('clue')->nullable();
            $table->text('note')->nullable();
            $table->text('tags')->nullable();

            $table->string('reporter_name')->nullable();
            $table->string('reporter_relation')->nullable();
            $table->string('reporter_phone', 40)->nullable();
            
            $table->integer('order')->default(0);
            
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->boolean('status')->default(0);    
        });

        Schema::create('category_missingchild', function(Blueprint $table)
        {   
            $table->increments('id');
            $table->integer('missingchild_id', false)->unsigned();
            $table->foreign('missingchild_id')->references('id')->on('missingchilds');
            $table->integer('category_id', false)->unsigned();
            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('article_missingchild', function(Blueprint $table)
        {
        	$table->increments('id');
        	$table->integer('article_id')->unsigned();
            $table->foreign('article_id')->references('id')->on('articles');
            $table->integer('missingchild_id')->unsigned();
            $table->foreign('missingchild_id')->references('id')->on('missingchilds');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('article_missingchild');
		Schema::drop('category_missingchild');
		Schema::drop('missingchilds');
	}

}