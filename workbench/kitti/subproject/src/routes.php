<?php
//Route::controller('plugin_inventory','PluginInventoryController');

Route::group(array('prefix' => 'plugin_inventory'), function()
{
    Route::get('/','PluginInventoryController@getLists'); //list by id
    Route::get('fields', 'PluginInventoryController@getFields');// table field
    Route::get('find','PluginInventoryController@getFind'); //search
    Route::post('/','PluginInventoryController@postCreate'); // create
    Route::put('/{id}','PluginInventoryController@getUpdate'); // update
    Route::delete('/{id}','PluginInventoryController@deleteData'); //delete
});

//create , update , delete
//Route::resource('plugin_inventory', 'PluginInventoryController');