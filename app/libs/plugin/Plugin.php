<?php namespace Libs\Plugin;

use Carbon\Carbon;
use \Validator;
use \DB;
use \Response;
use \Libs\Plugin\PluginInventory as PluginInventory;
use \Libs\Plugin\PluginMethod;

class Plugin
{

	public function install($params , $method) {
		$validator = Validator::make($params, PluginInventory::$rules['install']);
		if($validator->passes()){
			// check plugin install already.
			$rs = PluginInventory::check($params['name'])->get()->toArray();
			if($rs) return Response::message(400, 'Module have already install.');

			// insert new plugin_inventory
			$plugin = PluginInventory::create($params);

			if(is_array($method) && !empty($method)) {
				$count = count($method);
				for($i = 0 ; $i < $count ; $i++) {
					$method[$i]['plugin_id'] = $plugin->id;
				}
				// insert new plugin_method
				$plugin->plugin_method()->insert($method);
			}

			if($plugin) {
				return Response::result(array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	), 'id' => $plugin->id
				));
			}
		}

		return Response::message(400, $validator->messages()->first());
	}

	public function uninstall($param){

		if(!$param) return Response::message(400, 'Module name or ID is required.');
		$result = PluginInventory::del($param);
		
		if($result) return Response::message(200, 'Uninstall plugin '.$param.' success!'); 
		return Response::message(400, 'Module was uninstalled already.');
	}

	public function update($params) {
		$validator = Validator::make($params, PluginInventory::$rules['update']);

		if($validator->passes()) {
			$plugin = PluginInventory::find($params['id']);
			$id = $params['id'];
			foreach ($params as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $plugin[$key] ||
                    $key == 'appkey' ||
                    $key == 'id') 
                {
                    unset($params[$key]);
                }
            }

            if(!count($params))
                return Response::message(200, 'Nothing is update.');

            $result = PluginInventory::change($id, $params);

            if($result)
            	return Response::message(200, 'Updated plugin : id '.$id.' success!');
            else
            	return Response::message(400, 'Updated plugin : id '.$id.' failure!');
		}

		return Response::message(400, $validator->messages()->first());
	}

	public function create_method($params) {
		$validator = Validator::make($params, PluginInventory::$rules['create_method']);

		if($validator->passes()) {
			$plugin = PluginMethod::create($params);

			if($plugin) {
				return Response::result(array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	), 'id'=> $plugin->id
				));
			}
		}
		return Response::message(400, $validator->messages()->first());
	}

	public function delete_method($id) {

		if(!$id || !is_numeric($id)) return Response::message(400, 'id is required.');
		$result = PluginMethod::del($id);
		if($result) return Response::message(200, 'Delete plugin_method : '.$id.' success!'); 
	}

	public function update_method($params) {
		$validator = Validator::make($params, PluginInventory::$rules['update_method']);
		if($validator->passes()) {

			$id = $params['id'];

			$plugin = PluginMethod::find($id);

			foreach ($params as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $plugin[$key] ||
                    $key == 'appkey' ||
                    $key == 'id' ||
                    $key == 'plugin_id') 
                {
                    unset($params[$key]);
                }
            }

            if(!count($params))
                return Response::message(200, 'Nothing is update.');

            $result = PluginMethod::change($id, $params);
            if($result) {
	            return Response::message(200, 'Updated plugin_method : '.$id.' success!');
            } else {
            	return Response::message(400, 'Updated plugin_method : '.$id.' failure!');
			}
        }
    	return Response::message(400, $validator->messages()->first());
	}
}