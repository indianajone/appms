<?php

use Illuminate\Database\Migrations\Migration;

class CreateArticlesTables extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function($table) {
            // primary key
            $table->increments('id');

            //foreign key
            $table->integer('app_id', false)->unsigned();
            $table->foreign('app_id')->references('id')->on('applications');

            $table->string('title',200);
            $table->text('teaser')->nullable();
            $table->text('content');
            
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->integer('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('articles');
    }

}