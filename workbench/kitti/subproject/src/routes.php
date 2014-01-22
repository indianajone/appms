<?php
//Route::controller('plugin_inventory','PluginInventoryController');

Route::group(array('prefix' => 'method'), function()
{
//    Route::get('/','PluginInventoryController@getLists'); //list by id
//    Route::get('/','PluginInventoryController@test');
//    Route::get('fields', 'PluginInventoryController@getFields');// table field
//    Route::get('find','PluginInventoryController@getFind'); //search
//    Route::post('/','PluginInventoryController@postCreate'); // create
//    Route::put('/{id}','PluginInventoryController@getUpdate'); // update
//    Route::delete('/{id}','PluginInventoryController@deleteData'); //delete
    
    Route::get('add','PluginInventoryController@getAdd');
    Route::get('delete','PluginInventoryController@getDelete');
    Route::get('update','PluginInventoryController@getUpdate');
    Route::get('list','PluginInventoryController@getList');
    Route::get('addmethod','PluginInventoryController@addMethod');
    Route::get('xml','PluginInventoryController@getXML');
});

//create , update , delete
//Route::resource('plugin_inventory', 'PluginInventoryController');