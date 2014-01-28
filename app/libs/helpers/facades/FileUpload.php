<?php namespace Libs\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

class FileUpload extends Facade {
 
    protected static function getFacadeAccessor()
    {
        return new \Libs\Helpers\FileUpload;
    }
 
}