<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('devices', function(Blueprint $table)
            {
                $table->increments('id')->unique();
                $table->integer('member_id')->unsigned();
                $table->foreign('member_id')->references('id')->on('members');
                $table->integer('app_id')->unsigned();
                $table->foreign('app_id')->references('id')->on('applications');
                $table->string('name', 255);
                $table->string('model', 100)->nullable();
                $table->string('os', 100)->nullable();
                $table->string('version', 10)->nullable();
                $table->string('udid', 40);
                $table->string('token', 64);
                $table->string('identifier', 100);
                $table->timestamps();
                $table->boolean('status')->default(0);
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('devices', function(Blueprint $table)
		{
			//
		});
	}

}