<?php
namespace Core\Plugin\Facades;
use \DB;

class Plugin {

    // plugin inventory
    public static function createPluginInventoryArray($data) {
        if (is_array($data)) {
            //config required field
            $data_require = array('name', 'description', 'version', 'author', 'author_email', 'protected', 'status');
            $check = self::check_key($data_require, $data);

            if ($check == 'success') {
                $data = array_add($data, 'create_date', date('Y-m-d H:i:s'));
                $result = DB::table('plugin_inventory')
                        ->insertGetId($data);
                return $result;
            } else {
                return $check;
            }
        } else {
            return 'Format data must be array.';
        }
    }

    public static function readPluginInventory($offset = 0, $limit = 10) {
        $result = DB::table('plugin_inventory')
                ->skip($offset)
                ->take($limit)
                ->get();

        return $result;
    }

    public static function readPluginInventoryById($id) {
        if ($id) {
            $result = DB::table('plugin_inventory')
                    ->where('plugin_id', $id)
                    ->get();
            return $result;
        }
    }

    public static function updatePluginInventory($id, $data) {
        if (is_array($data) && $id != '') {
            $data = array_add($data, 'update_date', date('Y-m-d H:i:s'));
            $result = DB::table('plugin_inventory')
                    ->where('plugin_id', $id)
                    ->update($data);
            return $result;
        } else {
            return 'plugin_id and data are required.';
        }
    }

    public static function deletePluginInventory($id) {
        if ($id) {
            $result = DB::table('plugin_inventory')
                    ->where('plugin_id', $id)
                    ->delete();
            return $result;
        }
    }

    // plugin method
    public static function createPluginMethod($data) {
        if (is_array($data)) {
            //config required field
            $data_require = array('plugin_id','name', 'description');
            $check = self::check_key($data_require, $data);

            if ($check == 'success') {
                $result = DB::table('plugin_method')
                        ->insertGetId($data);
                return $result;
            } else {
                return $check;
            }
        } else {
            return 'Format of data must be array.';
        }
    }

    public static function readPluginMethod($offset = 0, $limit = 10) {
        $result = DB::table('plugin_method')
                ->skip($offset)
                ->take($limit)
                ->get();

        return $result;
    }

    public static function readPluginMethodById($id) {
        if ($id) {
            $result = DB::table('plugin_method')
                    ->where('method_id', $id)
                    ->get();
            return $result;
        }
    }

    public static function updatePluginMethod($id, $data) {
        if (is_array($data) && $id != '') {
            $result = DB::table('plugin_method')
                    ->where('method_id', $id)
                    ->update($data);
            return $result;
        } else {
            return 'method_id and data are required.';
        }
    }

    public static function deletePluginMethod() {
        if ($id) {
            $result = DB::table('plugin_inventory')
                    ->where('plugin_id', $id)
                    ->delete();
            return $result;
        }
    }
    
    public static function attachMethod($plugin_id) {
        $data = array_add($data, 'update_date', date('Y-m-d H:i:s'));
            $result = DB::table('plugin_inventory')
                    ->where('plugin_id', $id)
                    ->update(array('status' => 1));
            return $result;
    }
    
    public static function detachMethod($plugin_id) {
        $data = array_add($data, 'update_date', date('Y-m-d H:i:s'));
            $result = DB::table('plugin_inventory')
                    ->where('plugin_id', $id)
                    ->update(array('status' => 0));
            return $result;
    }

    public static function check_key($data_require, $data) {
        foreach ($data_require as $key) {
            if (!array_key_exists($key, $data)) {
                return 'Failure :: required ' . $key . ' field';
            }
        }
        return 'success';
    }
}
