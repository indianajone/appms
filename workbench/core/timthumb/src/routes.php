<?php

Route::group(array('prefix' => Config::get('timthumb::prefix')), function() 
{
    Route::get('{src}/{w?}/{h?}/{zc?}', function($src,$w=0,$h=0,$zc=1) 
    {	
        return Timthumb::get(asset(Config::get('image.slug').'/'.$src), $w, $h, $zc);
    });
});

