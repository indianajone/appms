<?php

use Illuminate\Database\Migrations\Migration;

class CreatePluginInventory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plugin_inventory', function($table) {
            $table->increments('id',4);
            $table->string('name',40);
            $table->string('description',100);
            $table->string('version',10);
            $table->string('author',40);
            $table->string('author_email',40);
            $table->boolean('protected');
            $table->boolean('status');
            $table->integer('created_at');
            $table->integer('updated_at');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('plugin_inventory');
	}

}