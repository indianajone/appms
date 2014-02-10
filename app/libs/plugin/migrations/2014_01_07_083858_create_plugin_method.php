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
            $table->increments('id',4);
            $table->integer('plugin_id', false)->unsigned();
   			$table->foreign('plugin_id')->references('id')->on('plugin_inventory')->onDelete('cascade');
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