<?php namespace Libs\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

class Image extends Facade {
 
    protected static function getFacadeAccessor()
    {
        return new \Libs\Helpers\Image;
    }
 
}