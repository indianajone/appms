<?php

use Illuminate\Database\Migrations\Migration;

class CreateGalleries extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('galleries', function($table) {
                $table->increments('id');
                $table->integer('app_id', false)->unsigned();
                $table->foreign('app_id')->references('id')->on('applications');
                $table->integer('content_id', false);
                $table->string('content_type', 10);
                $table->string('name',255);
                $table->string('description',255)->nullable();
                $table->string('picture',100)->nullable();
                $table->integer('like', false)->unsigned();
                $table->integer('created_at', false);
                $table->integer('updated_at', false);
                $table->integer('publish_at', false);
                $table->boolean('status')->default(1);
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::drop('galleries');
        }

}