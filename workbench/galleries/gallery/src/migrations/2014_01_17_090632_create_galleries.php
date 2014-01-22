<?php

use Illuminate\Database\Migrations\Migration;

class CreateGalleries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('galleries', function($table) {
                    $table->increments('gallery_id');
                    $table->integer('app_id', false);
                    $table->integer('content_id', false);
                    $table->string('type', 10);
                    $table->string('name',255);
                    $table->string('description',255);
                    $table->string('picture',100);
                    $table->integer('like', false);
                    $table->timestamps();
                    $table->timestamp('publish_date');
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
		Schema::drop('galleries');
	}

}