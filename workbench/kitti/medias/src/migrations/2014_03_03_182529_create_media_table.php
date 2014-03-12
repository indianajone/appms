<?php

use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('medias', function($table) {                        
            $table->increments('id');
            $table->integer('app_id', false)->unsigned();
            $table->foreign('app_id')->references('id')->on('applications');
            $table->integer('gallery_id', false)->unsigned();
			$table->foreign('gallery_id')->references('id')->on('galleries');

            $table->string('name',255);
            $table->string('description',255)->nullable();
            $table->string('picture',100);
            $table->string('link', 255)->nullable();

            $table->string('type',10);
            $table->string('latitude',25)->nullable();
            $table->string('longitude',25)->nullable();
            $table->integer('like', false)->unsigned()->default(0);
            $table->integer('created_at', false);
            $table->integer('updated_at', false);
            $table->integer('deleted_at', false)->nullable();

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('medias');
	}

}