<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissingchildTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('missingchilds', function(Blueprint $table)
            {
                $table->increments('id')->unique();
                $table->integer('member_id')->unsigned();
                $table->foreign('member_id')->references('id')->on('members');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->text('place_of_missing');
                $table->string('place_of_report', 100);
                $table->string('reporter', 40);
                $table->string('relationship', 40);
                $table->text('note');
                $table->boolean('approved')->default(0);
                $table->boolean('follow')->default(0);
                $table->boolean('founded')->default(0);
                $table->boolean('public')->default(0);
                $table->integer('order');
                $table->integer('create_at');
                $table->integer('update_at');
                $table->integer('missing_date');
                $table->integer('report_date');
                $table->boolean('status')->default(0);
            });
            
            Schema::create('article_missingchild', function(Blueprint $table)
            {
                $table->increments('id')->unique();
                $table->integer('article_id')->unsigned();
                $table->foreign('article_id')->references('id')->on('articles');
                $table->integer('missingchild_id')->unsigned();
                $table->foreign('missingchild_id')->references('id')->on('missingchilds');
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
		Schema::drop('missingchildren', function(Blueprint $table)
		{
			//
		});
	}

}