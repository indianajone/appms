<?php namespace Kitti\Subproject\Models;

class PluginInventory extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'plugin_inventory';
        protected $primaryKey = "plugin_id";
        public $timestamps = false;
}