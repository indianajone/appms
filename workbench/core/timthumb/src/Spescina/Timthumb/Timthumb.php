<?php namespace Spescina\Timthumb;

use Config;
use Spescina\Timthumb\TimthumbExt;

define ('DEBUG_ON', Config::get('timthumb::debug_on'));
define ('DEBUG_LEVEL', Config::get('timthumb::debug_level'));
define ('FILE_CACHE_ENABLED', Config::get('timthumb::file_cache_enabled'));
define ('FILE_CACHE_DIRECTORY', Config::get('timthumb::file_cache_directory'));
define ('NOT_FOUND_IMAGE', Config::get('timthumb::not_found_image'));
define ('ERROR_IMAGE',  Config::get('timthumb::error_image'));
define ('PNG_IS_TRANSPARENT', Config::get('timthumb::png_is_transparent'));

class Timthumb {
    public function get($src, $w = 0,$h = 0,$zc = 3) 
    {        
        $params = array(
            'src' => str_replace('-', '/', $src),
            'w' => $w,
            'h' => $h,
            'zc' => $zc
        );

        return TimthumbExt::start($params);

    }
        
    public function link($src,$w = 0,$h = 0,$zc = 1) 
    {  
        return  Config::get('timthumb::prefix').$src.'/'.$w.'/'.$h.'/'.$zc;;
    }
}
