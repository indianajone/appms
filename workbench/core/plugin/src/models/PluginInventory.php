<?php namespace Core\Plugin\Models;

//use Core\Plugin\Models\PluginMethod;

class PluginInventory extends \Illuminate\Database\Eloquent\Model {
    
    protected $table = 'plugin_inventory';
    protected $fillable = array('name', 'description', 'version','author','author_email','protected','status');
    protected $primaryKey = "plugin_id";
    public $timestamps = false;
    
    
    public function plugin_method() {
        return $this->hasMany('Core\Plugin\Models\PluginMethod','plugin_id');
    }
}