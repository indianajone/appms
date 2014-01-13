<?php

use Kitti\Subproject\Models\PluginInventory;
use Core\Plugin\Controllers\Plugin;
class PluginInventoryController extends BaseController {

    public function getAdd() {
        $plugin = new Plugin();
        
        $inv = array(
            'name' => 'name12222',
            'description' => 'desc22323ription',
            'version' => '0.1',
            'author' => 'auth43241or',
            'author_email' => 'author3241234234_email',
            'protected' => '1',
            'status' => '1'
        );
//        
//        $method = array(
//            array(
//                'name' => 'd',
//                'description' => 'e'
//            ),
//            array(
//                'name' => 'c',
//                'description' => 'e'
//            ),
//            array(
//                'name' => 'gdsa',
//                'description' => 'dsfasdf'
//            )
//        );
        
        return $plugin->install($inv);
        //return $plugin->install($inv,$method);
        
        //return $plugin->uninstall();
    }
    
    public function getList() {
        $plugin = new Plugin();
        // all
        return $plugin->show(23);
        // by id
        //return $plugin->list(10);
    }
    
    public function getUpdate() {
        $plugin = new Plugin();
        
        $inv = array(
            'name' => 'update2',
            'description' => 'up',
            'version' => '0.2',
            'author' => 'up',
            'author_email' => 'up@gmail.com',
            'protected' => '0',
            'status' => '0'
        );
        return $plugin->update(21,$inv);
    }
    
    public function getDelete() {
        $plugin = new Plugin();
        return $plugin->uninstall(45);
    }

    public function addMethod() {
        $method = array(
            array(
                'plugin_id' => 22,
                'name' => 'new',
                'description' => 'new'
            ),
            array(
                'plugin_id' => 22,
                'name' => 'new2',
                'description' => 'new2'
            ),
            array(
                'plugin_id' => 23,
                'name' => 'new',
                'description' => 'new'
            )
        );
        
        $plugin = new Plugin();
        
        return $plugin->add_method($method);
    }
    
    public function getXML() {
        $inv = array(
            'name' => 'name12222',
            'description' => 'desc22323ription',
            'version' => '0.1',
            'author' => 'auth43241or',
            'author_email' => 'author3241234234_email',
            'protected' => '1',
            'status' => '1'
        );
        header("Content-type: text/xml");
        return Response::xml($inv,'root');
    }
}
