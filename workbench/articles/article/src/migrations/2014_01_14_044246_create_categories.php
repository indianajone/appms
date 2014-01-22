<?php

use Illuminate\Database\Migrations\Migration;

class CreateCategories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function($table) {
                    $table->increments('category_id');
                    $table->integer('app_id', false);
                    $table->integer('parent_id', false);
                    $table->string('name', 255);
                    $table->string('description',255);
                    $table->bigInteger('lft', false);
                    $table->bigInteger('rgt', false);
                    $table->bigInteger('depth', false);
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
		Schema::drop('categories');
	}

}