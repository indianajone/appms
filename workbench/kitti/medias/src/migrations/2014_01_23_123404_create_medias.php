<?php

use Illuminate\Database\Migrations\Migration;

class CreateMedias extends Migration {

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

            $table->integer('member_id', false)->unsigned();
            $table->foreign('member_id')->references('id')->on('members');

            $table->integer('gallery_id', false)->unsigned();
            $table->foreign('gallery_id')->references('id')->on('galleries');

            $table->string('name',255);
            $table->string('description',255)->nullable();
            $table->string('path',100)->nullable();;
            $table->string('filename',100)->nullable();;
            $table->string('type',10);
            $table->string('latitude',25)->nullable();
            $table->string('longitude',25)->nullable();
            $table->integer('like', false)->unsigned();
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
		Schema::drop('medias');
	}

}