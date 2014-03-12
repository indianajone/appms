<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('app_id')->unsigned();
            $table->foreign('app_id')->references('id')->on('applications');
            $table->integer('parent_id')->nullable();
            $table->string('fbid', 40)->nullable();
            $table->text('fbtoken')->nullable();
            $table->string('username', 40)->unique();
            $table->string('password', 100);
            $table->string('title', 10)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('other_name', 40)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('mobile', 10)->nullable();
            $table->boolean('verified')->nullable()->default(0);
            $table->string('email', 40);
            $table->string('address', 255)->nullable();
            $table->string('gender', 10)->nullable();
            $table->integer('birthday')->nullable();
            $table->string('description', 255)->nullable();
            $table->string('type', 10);
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->integer('deleted_at')->nullable();
            $table->integer('last_seen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('members');
    }

}