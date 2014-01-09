<?php

use Kitti\Subproject\Models\PluginInventory;

class PluginInventoryController extends BaseController {

    public function getLists() {
        $plugin = new Plugin();
        $data = array(
            'plugin_id' => 1,
            'name' => '1111',
            'description' => '44444'
        );
        return $plugin->createPluginMethod($data);
        //return $plugin->readPluginInventory(0,3);
//        return $plugin->readPluginInventoryById(6);
//        $data = array(
//            'name'  => 'name',
//            'description' => 'desc',
//            'version' => 12,
//            'author' => 'name',
//            'author_email' => 'name@example.com'
//        );
        //$result = $plugin->createPluginInventory($data);
//        $result = $plugin->updatePluginInventory(5,$data);
//        return $result;
//        $status['code'] = 200;
//        $status['message'] = 'success';
//
//        if (Input::has('appkey')) {
//            $plugin = PluginInventory::where('plugin_id', '=', Input::get('appkey'))->first()->toArray();
//        } else {
//            $offset= Input::get('offset',0);
//            $limit = Input::get('limit',10);
//            $plugin = PluginInventory::all()->skip($offset)->take($limit)->toArray();
//        }
//
//        $response = Response::listing($status, $plugin);
//        if (Input::has('format')) 
//            {
//            $format = Input::get('format');
//            if ($format == 'xml') {
//                return Response::xml($response, 'root');
//            } else {
//                return $response;
//            }
//        } else {
//            return $response;
//        }
    }

    public function postCreate() {
        $plugin = new PluginInventory();

        //for test
        //http://localhost/dev/appms/public/plugin_inventory/create?name=a&description=b&version=1&author=a&author_email=a@admin.com&protected=1
        // $curl -k -X POST http://localhost/dev/appms/public/plugin_inventory -d name=name -d description=description -d version=2 -d author=a -d author_email=a@email.com -d protected=1

        $plugin->name = Input::get('name');
        $plugin->description = Input::get('description');
        $plugin->version = Input::get('version');
        $plugin->author = Input::get('author');
        $plugin->author_email = Input::get('author_email');
        $plugin->protected = Input::get('protected');
        $plugin->create_date = date('Y-m-d H:i:s');

        $result = $plugin->save();

        if ($result) {
            $status['code'] = 200;
            $status['message'] = 'success';
            return Response::message($status, 'json');
        } else {
            $status['code'] = 400;
            $status['message'] = 'Insert data fail.:: query error';
            return Response::message($status, 'json');
        }
    }

    public function putUpdate($id) {

//        $input = Input::all();
//        $input['update_date'] = date('Y-m-d H:i:s');
//        $result = DB::table('plugin_inventory')
//                ->where('plugin_id', $id)
//                ->update($input);
        //$name = Input::get('name');
        
        $plugin = PluginInventory::where('plugin_id','=', $id)->first();
        $plugin->name = Input::get('name');
        $plugin->description = Input::get('description');
        $plugin->version = Input::get('version');
        $plugin->author = Input::get('author');
        $plugin->author_email = Input::get('author_email');
        $plugin->protected = Input::get('protected');
        $plugin->update_date = date('Y-m-d H:i:s');
        $result = $plugin->save();
        
        if ($result) {
            $status['code'] = 200;
            $status['message'] = 'success';
            return Response::message($status, 'json');
        } else {
            $status['code'] = 400;
            $status['message'] = 'Update data fail.:: query error';
            return Response::message($status, 'json');
        }
    }
    
    public function deleteData($id) {
        //echo $id;
        $result = PluginInventory::where('plugin_id','=', $id)->first()->delete();
        if ($result) {
            $status['code'] = 200;
            $status['message'] = 'success';
            return Response::message($status, 'json');
        } else {
            $status['code'] = 400;
            $status['message'] = 'Update data fail.:: query error';
            return Response::message($status, 'json');
        }
    }

    public function getFields() {
        return Response::fields('plugin_inventory');
    }

}
