<?php

use Illuminate\Database\Migrations\Migration;

class CreatePluginMethod extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plugin_method', function($table) {
                    $table->increments('method_id',4);
                    $table->integer('plugin_id',false);
                    $table->string('name',40);
                    $table->string('description',100);
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('plugin_method');
	}

}