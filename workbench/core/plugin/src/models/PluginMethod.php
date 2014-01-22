<?php namespace Core\Plugin\Models;

class PluginMethod extends \Illuminate\Database\Eloquent\Model {
    
    protected $table = 'plugin_method';
    protected $primaryKey = "method_id";
    public $timestamps = false;
}