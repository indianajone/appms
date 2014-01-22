<?php

namespace Core\Plugin\Controllers;

use Core\Plugin\Models\PluginInventory;
use Core\Plugin\Models\PluginMethod;

class Plugin {
    
    public function install($inv, $method = null) {
        if (is_array($inv)) {
            //config required field
            $data_require = array('name', 'description', 'version', 'author', 'author_email', 'protected', 'status');
            $check = self::check_key($data_require, $inv);

            if ($check == 'success') {
                $data = array_add($inv, 'create_date', date('Y-m-d H:i:s'));
                $plugin = PluginInventory::create($data);
                
                $plugin_id = $plugin->plugin_id;
            } else {
                return $check;
            }
            
            if($method != null) {
                if(is_array($method)) {
                    foreach ($method as $data) {
                        $method = new PluginMethod;
                        $method->plugin_id = $plugin_id;
                        $method->name = $data['name'];
                        $method->description = $data['description'];
                        $method->save();
                    }
                    
                    return PluginInventory::with('plugin_method')->where('plugin_id','=',$plugin_id)->get();
                }
            } else {
                return $plugin;
            }
        } else {
            return 'Format data must be array.';
        }
    }
    
    public function update($plugin_id, $data) {
        if(is_array($data)) {
            $data = array_add($data, 'update_date', date('Y-m-d H:i:s'));
            return PluginInventory::where('plugin_id','=',$plugin_id)->update($data);
        }
    }
    
    public function show($plugin_id = null) {
        if($plugin_id == null) {
            return PluginInventory::with('plugin_method')->get();
        } else {
            return PluginInventory::with('plugin_method')->where('plugin_id','=',$plugin_id)->get();
        }
    }
    
    public function uninstall($plugin_id) {
         $result = PluginInventory::where('plugin_id','=',$plugin_id)->delete();
         if($result) {
             $result2 = PluginMethod::where('plugin_id','=',$plugin_id)->delete();
             return $result2;
         } else {
             return false;
         }
    }

    public function add_method($data) {
        if(is_array($data)) {
            return PluginMethod::create($data);
        }
    }
    
    public function delete_method($method_id) {
        if($method_id) {
            return PluginMethod::where('method_id','=',$method_id)->delete();
        }
    }
    
    public function update_method($method_id, $data) {
        if(is_array($data)) {
            return PluginMethod::where('method_id','=',$method_id)->update($data);
        }
    }

    // HELPER FUNCTION
    public static function check_key($data_require, $data) {
        foreach ($data_require as $key) {
            if (!array_key_exists($key, $data)) {
                return 'Failure :: required ' . $key . ' field';
            }
        }
        return 'success';
    }

}
