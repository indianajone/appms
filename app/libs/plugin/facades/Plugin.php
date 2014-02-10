<?php namespace Libs\Plugin\Facades;

use Illuminate\Support\Facades\Facade;

class Plugin extends Facade {
 
    protected static function getFacadeAccessor()
    {
        return new \Libs\Plugin\Plugin;
    }
 
}