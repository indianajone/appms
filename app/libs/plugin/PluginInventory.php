<?php namespace Libs\Plugin;

use \BaseModel;

class PluginInventory extends BaseModel {

	protected $table = 'plugin_inventory';

    protected $guarded = array('id');

	public static $rules = array(
		'install' => array(
			'name' 	=> 'required',
			'description' => 'required',
			'version' => 'required',
			'author' => 'required',
			'author_email' => 'required',
			'protected' => 'required'
		),
		'uninstall' => array(
			'id' => 'required'
		),
		'update' => array(
			'id' => 'required'
		),
		'create_method' =>array(
			'plugin_id' => 'required',
			'name' => 'required'
		),
		'update_method' => array(
			'id' => 'required'
		)
	);

	public function scopeCheck($query, $name) {
		return $query->whereName($name);
	}

	public function plugin_method(){
		return $this->hasMany('Libs\\Plugin\\PluginMethod' , 'plugin_id' , 'id');
	}

	public function scopeDel($query, $param){
		if(is_numeric($param)) {
			$result = $query->where('id' , '=' , $param)->delete();
		} else {
			$result = $query->where('name' , '=' , $param)->delete();
		}
		return $result;
	}

	public function scopeChange($query , $id , $params){
		return $query->where('id', '=' , $id)->update($params);
	}
}