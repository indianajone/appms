<?php namespace Libs\Helpers;

use App, Config, File, Input, Response;
use Kitti\Medias\Media;

class Image 
{
	public function upload($base64)
	{
		$local_env = App::environment('local');

		$cdn = Config::get('image.cdn');
		$nas = Config::get('image.nas');
		$slug = Config::get('image.slug').'/'.date('Y/m/d').'/';
		$path = $cdn.$nas.$slug;
		$format = Config::get('image.format');

		// #FIXED iDevice base64 encode.
		$picture = base64_decode(str_replace('%2B','+', $base64));

		if($format == 'auto' || $format == '')
		{
			$f = finfo_open();
			$mime_type = finfo_buffer($f, $picture, FILEINFO_MIME_TYPE);
			if(!starts_with($mime_type, 'image'))
				return Response::json(array(
					'header'=> [
		        		'code'=> 400,
		        		'message'=> 'The file is not an image. Please check you image data'
		        	]
				), 200);
			
			$format = $mime_type == 'image/jpeg' ? 'jpg' : 'png';
		}
		$filename = rand(0,1000).time().'.'.$format;
		
		if(!File::exists($path))
			File::makeDirectory($path, 0777, true, true);
		if(!File::exists($path))
			return Response::json(array(
				'header'=> [
	        		'code'=> 400,
	        		'message'=> 'Can not create folder. Please check folder permission.'
	        	]
			), 200);
		if(File::put($path.$filename,$picture))
			return $cdn.$slug.$filename;
		else
			return Response::json(array(
				'header'=> [
	        		'code'=> 400,
	        		'message'=> 'Can not upload picture. Please check your file data.'
	        	]
			), 200);
	}

	public function delete($url)
	{
		// $local_env = App::environment('local');
		// $cdn = $local_env ? Config::get('image.cdn') : '';
		// $nas = $local_env ? Config::get('image.nas') : '';
		$path = parse_url($url, PHP_URL_PATH);

		// if(File::exists($cdn.$nas.$path))
		if(File::exists($path))
		{
			File::delete($path);
			// File::delete($cdn.$nas.$path);
		}
	}
}