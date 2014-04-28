<?php

use Illuminate\Database\Migrations\Migration;

class CreateMedias extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function($table) {                        
            $table->increments('id');
            $table->string('name',255);
            $table->string('description',255)->nullable();
            $table->string('file',100);
            $table->string('link', 255)->nullable();
            $table->string('filename',100)->nullable();
            $table->string('mime_type', 100)->default('');
            $table->string('latitude',25)->nullable();
            $table->string('longitude',25)->nullable();
            $table->integer('like', false)->unsigned()->default(0);
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
        Schema::drop('medias');
    }

}