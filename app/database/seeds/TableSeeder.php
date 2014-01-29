<?php

use \Kitti\Galleries\Gallery;
use \Carbon\Carbon;

class TableSeeder extends Seeder {

    public function run()
    {
        // DB::table('galleries')->delete();

        $data = array(
        	array(
	        	'app_id' => 1,
	        	'gallery_id' => 3,
	        	// 'content_id' => 1,
	        	'type' => 'image',
	        	'name' => 'Image 2',
	        	'description' => 'A Test Gallery2',
	        	// 'like' => 0,
	        	'created_at' => Carbon::now()->timestamp,
	        	'updated_at' => Carbon::now()->timestamp,
	        	// 'publish_at' => Carbon::now()->timestamp,
	        	'status' => 1
	         ),
	     );

        DB::table('medias')->insert($data);
    }
}