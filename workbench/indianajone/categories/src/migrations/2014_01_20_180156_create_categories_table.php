<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('categories', function(Blueprint $table) {
      // These columns are needed for Baum's Nested Set implementation to work.
      // Column names may be changed, but they *must* all exist and be modified
      // in the model.
      // Take a look at the model scaffold comments for details.
      $table->increments('id');
      $table->integer('parent_id')->nullable();
      $table->integer('lft')->nullable();
      $table->integer('rgt')->nullable();
      $table->integer('depth')->nullable();

      // Add needed columns here (f.ex: name, slug, path, etc.)
      $table->string('name', 100);
      $table->string('description')->nullable();
      $table->integer('app_id')->unsigned();
      $table->foreign('app_id')->references('id')->on('applications');
      $table->integer('created_at');
      $table->integer('updated_at');

      // Default indexes
      // Add indexes on parent_id, lft, rgt columns by default. Of course,
      // the correct ones will depend on the application and use case.
      $table->index('parent_id');
      $table->index('lft');
      $table->index('rgt');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('categories');
  }

}
