<?php namespace Libs\Helpers;

class FileUpload
{
	public function upload($base64)
	{
		$cdn = \Config::get('file.cdn');
		$nas = \Config::get('file.nas');
		$slug = \Config::get('file.slug');
		//$slug = \Config::get('file.slug').'/'.date('Y/m/d').'/';
		//$path = $cdn.$nas.$slug;
		$format = \Config::get('file.format');

		// #FIXED iDevice base64 encode.
		$media = base64_decode(str_replace('%2B','+', $base64));

		if($format == 'auto' || $format == '')
		{
			$f = finfo_open();
			$mime_type = finfo_buffer($f, $media, FILEINFO_MIME_TYPE);
			if(starts_with($mime_type, 'image')) {
				$folder = '/image';
				switch($mime_type) {
					case 'image/jpg':
						$format = 'jpg';break;
					default:
						$format = 'png';break;
				}
			} else if (starts_with($mime_type, 'video')) {
				$folder = '/video';
				$format = $mime_type == 'video/x-ms-asf'? 'wma' : 'avi';
				switch($mime_type) {
					case 'video/x-ms-asf':
						$format = 'wma';break;
					case 'video/3gpp':
						$format = '3gp';break;
					case 'video/quicktime':
						$format = 'mov';break;
					default:
						$format = 'mp4';break;
				}
			} else if (starts_with($mime_type, 'audio')) {
				$folder = '/audio';
				switch($mime_type) {
					case 'audio/mpeg':
						$format = 'mp3';break;
					default:
						$format = 'mp3';break;break;
				}
			} else {
				return \Response::json(array(
				'header'=> [
	        		'code'=> 400,
	        		'message'=> 'Can not upload medias. Please check your file data.'
	        	]
			), 200);
			}
		}

		$slug = $slug.$folder.'/'.date('Y/m/d').'/';
		$path = $cdn.$nas.$slug;


		$filename = rand(0,1000).time().'.'.$format;
		if(!\File::exists($path))
			\File::makeDirectory($path, 0777, true, true);
		if(!\File::exists($path))
			return \Response::json(array(
				'header'=> [
	        		'code'=> 400,
	        		'message'=> 'Can not create folder. Please check folder permission.'
	        	]
			), 200);
		if(\File::put($path.$filename,$media)) {
			$response['type'] = str_replace('/', '', $folder);
			$response['path'] = $slug;
			$response['filename'] = $filename;

			//return asset($slug.$filename);
			return $response;
		} else {
			return \Response::json(array(
				'header'=> [
	        		'code'=> 400,
	        		'message'=> 'Can not upload medias. Please check your file data.'
	        	]
			), 200);
		}
	}
}