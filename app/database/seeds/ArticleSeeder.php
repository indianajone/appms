<?php namespace Kitti\Articles;

use Faker\Factory as Faker;

class ArticleSeeder extends \Seeder {

   public function run()
   {
   	Article::truncate();

   	$fake = Faker::create();

   	foreach (range(1, 20) as  $index) {
   		Article::create(array(
   			'app_id' 	=> 1,
   			'title'		=> $fake->sentence(3),
   			'content'	=> $fake->paragraph(4)
   		));
   	}
   }
}