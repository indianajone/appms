<?php namespace Libs\Plugin;

use \BaseModel;

class PluginMethod extends BaseModel {
	public static $key = 'id';
	protected $table = 'plugin_method';
    protected $guarded = array('id');
    public $timestamps = false;

    public function plugin_inventory() {
    	return $this->belongsTo('Libs\\Plugin\\PluginInventory');
    }

    public function scopeDel($query, $id){
    	return $query->where('id','=',$id)->delete();
    }

    public function scopeCheck($query , $params) {
    	return $query->where('id','=', $params['id'])->first();
    }

    public function scopeChange($query , $id , $params){
		return $query->where('id', '=' , $id)->update($params);
	}
}