// <?php
// namespace Core\Plugin\Facades;
// use \DB;
// use \Validator;
// use \Core\Plugin\Models\PluginInventory;
// use \Core\Plugin\Models\PluginMethod;
// use \Response;

// class Plugin {

//     public static function install($inv, $method = null) {

//             $validator = Validator::make(
//             $inv, array(
//                 'name' => 'required',
//                 'description' => 'required',
//                 'version' => 'required',
//                 'author' => 'required',
//                 'author_email' => 'required',
//                 'protected' => 'required',
//                 'status' => 'required'
//                 )
//             );

//             if ($validator->passes()) {

//                 $plugin = PluginInventory::create($inv);
//                 $plugin_id = $plugin->id;

//                 if($method != null) {
//                     if(is_array($method)) {
//                         foreach ($method as $data) {
//                             $method = new PluginMethod;
//                             $method->plugin_id = $plugin_id;
//                             $method->name = $data['name'];
//                             $method->description = $data['description'];
//                             $method->save();
//                         }
                        
//                         PluginInventory::with('plugin_method')->where('plugin_id','=',$plugin_id)->get();
//                     }
//                 }

//                 $response = array(
//                     'header' => array(
//                         'code' => '200',
//                         'message' => 'success'),
//                     'id' => $plugin_id
//                 );

//             } else {
//                 $response = array(
//                     'header' => array(
//                         'code' => '400',
//                         'message' => $validator->messages()->first()
//                 ));
//             }

//             return Response::result($response,'json');
//     }
    
//     public static function update($plugin_id, $data) {
//         if(is_array($data)) {
//             //$data = array_add($data, 'update_date', date('Y-m-d H:i:s'));
//             return PluginInventory::where('plugin_id','=', $plugin_id)->update($data);
//         }
//     }
    
//     public static function show($plugin_id = null) {
//         if($plugin_id == null) {
//             return PluginInventory::with('plugin_method')->get();
//         } else {
//             return PluginInventory::with('plugin_method')->where('plugin_id','=',$plugin_id)->get();
//         }
//     }
    
//     public static function uninstall($name) {
//          $result = PluginInventory::where('id','=',$plugin_id)->delete();
//          if($result) {
//              $result2 = PluginMethod::where('plugin_id','=',$plugin_id)->delete();
//              $response = array(
//                 'header' => array(
//                     'code' => '200',
//                     'message' => 'success')
//             );
//             return Response::result($response,'json');
//          }
//     }

//     public static function add_method($data) {
//         if(is_array($data)) {
//             return PluginMethod::create($data);
//         }
//     }
    
//     public static function delete_method($method_id) {
//         if($method_id) {
//             return PluginMethod::where('method_id','=',$method_id)->delete();
//         }
//     }
    
//     public static function update_method($method_id, $data) {
//         if(is_array($data)) {
//             return PluginMethod::where('method_id','=',$method_id)->update($data);
//         }
//     }

// }
