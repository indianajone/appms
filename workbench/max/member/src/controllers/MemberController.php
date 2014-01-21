<?php
namespace Max\Member\Controllers;

use Validator, Input, Response, Member;

class MemberController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            $offset = Input::get('offset', 0);
            $limit= Input::get('limit', 10);
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : $field;
            $members = Member::take($limit)->skip($offset)->get($fields);
            
            foreach ($members as $member) {
                $member->apps;
            }
            
            if($members->count() > 0)
                return Response::listing(array(
                        'code'=>200, 
                        'message'=>'success'
                ), $members, $offset, $limit);
            else 
                return Response::listing(array(
                        'code'=>204,
                        'message'=>'no content'
                ), null, $offset, $limit);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            $rules = array(
                'username'  => 'required|unique:members,username',
                'password'  => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'type' => 'required',
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));                
            }else{
                date_default_timezone_set('Asia/Bangkok');

                $appkey = Input::get('appkey');
                $apps = Application::select('id')->where('appkey', '=', $appkey)->get();
                foreach ($apps as $app) {
                    $app_id = $app->id;
                }
                
                $member = new Member();       
                $member->app_id = $app_id;
                $member->parent_id = Input::get('parent_id', 0);
                $member->fbid = Input::get('fbid');
                $member->fbtoken = Input::get('fbtoken');
                $member->username = Input::get('username');
                $member->password = Hash::make(Input::get('password'));
                $member->title = Input::get('title');
                $member->first_name = Input::get('first_name');
                $member->last_name = Input::get('last_name');
                $member->other_name = Input::get('other_name');
                $member->phone = Input::get('phone');
                $member->mobile = Input::get('mobile');
                $member->otp = rand(0000, 9999);
                $member->verified = Input::get('verified');
                $member->email = Input::get('email');
                $member->address = Input::get('address');            
                $member->gender = Input::get('gender');
                $member->birthday = Input::get('birthday');       
                $member->description = Input::get('description');    
                $member->type = Input::get('type');
                $member->status = Input::get('status', 0);
                
                $result = $member->save();

                if($result){ 
                    $arrResp['header']['code'] = 200;
                    $arrResp['header']['message'] = 'Success';
                    $arrResp['id'] = $member->id;                
                    return Response::json($arrResp);
                } 
                else {    
                    $arrResp['header']['code'] = 204;
                    $arrResp['header']['message'] = 'Cannot insert data';
                    return Response::json($arrResp);
                }            
            }
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : '*';
            $member = Member::with('apps')->select($fields)->where('id', '=', $id)->get();
            
            if($member)
                return Response::json(array(
                    'header'=> array(
                        'code'=>200, 
                        'message'=>'success'
                    ),
                    'entries'=> [$member->toArray()]
            ));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            $rules = array(
                'email' => 'required|email|exists:members,email,user_id,'.$id,
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            }
            
            $member = Member::find($id);
            
            $parent_id = Input::get('parent_id', $member->parent_id);
            $fbid = Input::get('fbid', $member->fbid);
            $fbtoken = Input::get('fbtoken', $member->fbtoken);
            $username = Input::get('username', $member->username);
            $password = Input::get('password', $member->password);
            $title = Input::get('title', $member->title);
            $first_name = Input::get('first_name', $member->first_name);
            $last_name = Input::get('last_name', $member->last_name);
            $other_name = Input::get('other_name', $member->other_name);
            $phone = Input::get('phone', $member->phone);
            $mobile = Input::get('mobile', $member->mobile);
            $otp = Input::get('otp', $member->otp);
            $verified = Input::get('verified', $member->verified);
            $email = Input::get('email', $member->email);
            $address = Input::get('address', $member->address);            
            $gender = Input::get('gender', $member->gender);
            $birthday = Input::get('birthday', $member->birthday);
            $description = Input::get('description', $member->description);    
            $type = Input::get('type', $member->type);
            $status = Input::get('status', $member->status);
            
            date_default_timezone_set('Asia/Bangkok');
            
            if($member){
                $result = $member->where('id', '=', $id)->update(array(
                    'parent_id' => $parent_id,
                    'fbid' => $fbid,
                    'fbtoken' => $fbtoken,
                    'username' => $username,
                    'password' => $password,
                    'title' => $title,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'other_name' => $other_name,
                    'phone' => $phone,
                    'mobile' => $mobile,
                    'otp' => $otp,
                    'verified' => $verified,
                    'email' => $email,
                    'address' => $address,
                    'gender' => $gender,
                    'birthday' => $birthday,
                    'description' => $description,
                    'type' => $type,
                    'status' => $status,
                ));

                if($result)
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        ),
                        'id'=> $member->id
                    ));
                else
                    return 'some error';
            } else {
                return Response::json(array(
                    'code'=>204, 
                    'message'=>'no member found to update'
                ));
            }
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
            $member = Member::find($id);
            if($member) $member->delete();
	}
        
        public function fields($table, $format='json')
        {
            return Response::fields($table, $format);
        }

        public function hasParent(){
            $member_id = Input::get('member_id');
            $parent_id = Member::select('parent_id')->where('id', '=', $member_id)->get();
            
            return Response::json(array(
                'header'=> array(
                    'code' => 200, 
                    'message' => 'success'
                ),
                'parent_id' => [$parent_id->toArray()]
            ));
        }
        
        public function doLogin(){            
            $rules = array(
                'username'  => 'required|exists:members,username',
                'password' => 'required',
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            } else {
                $username = Input::get('username');
                $password = Input::get('password');
                
                $members = Member::select('password')->where('username', '=', $username)->get();
                foreach ($members as $member) {
                    $chk_pwd = $member->password;
                }
                
                if (Hash::check($password, $chk_pwd))
                    return Response::json(array(
                        'header'=> array(
                                'code' => 200, 
                                'message' => 'success'
                        )
                    ));
                else
                    return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => 'username / password incorrect'
                            )
                    ));        
            }
        }
        
        public function doLogout(){
            Session::flush();
            return Response::json(array(
                'header'=> array(
                    'code' => 200, 
                    'message' => 'success'
                    )
                ));
        }
        
        public function resetPassword(){
            $rules = array(
                'username'    => 'required|exists:members,username',
                'password' => 'required',
                'new_password' => 'required'
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            }
            
            $userdata = array(
                'username' 	=> Input::get('username'),
                'password' 	=> Input::get('password')
            );
            
            $new_password = Input::get('new_password');
            
            if (Auth::attempt($userdata)) {                
                $user = Member::where('username', '=', $userdata['username']);
                if($user){
                    $result = $user->update(array(
                        'password' => Hash::make($new_password)
                    ));
                }
                
                if($result){
                    return Response::json(array(
                        'header'=> array(
                                'code' => 200, 
                                'message' => 'success'
                        )
                    ));
                }else{
                    return Response::json(array(
                        'header'=> array(
                                'code' => 204, 
                                'message' => 'username does not exist'
                        )
                    ));
                }
            } else {
                return Response::json(array(
                    'header'=> array(
                        'code' => 204, 
                        'message' => 'username / password incorrect'
                        )
                ));
            }
        }
        
//        public function registerDevice($id){      
//            $device_id = Device::find($id);
//            if(count($device_id == 0)){
//                $rules = array(
//                    'name'  => 'required',
//                    'udid'  => 'required',
//                    'token' => 'required',
//                    'identifier' => 'required'
//                );
//
//                $validator = Validator::make(Input::all(), $rules);
//
//                if ($validator->fails()) {
//                    $msg = $validator->messages()->first();
//                    return Response::json(array(
//                            'header'=> array(
//                                'code' => 204, 
//                                'message' => $msg
//                        )
//                    ));
//                }else{
//                    $members = Member::select('app_id')->where('id', '=', $id)->get();
//                    foreach ($members as $member) {
//                        $app_id = $member->app_id;
//                    }
//
//                    $devices = new Device();     
//                    $devices->member_id = $id;
//                    $devices->app_id = $app_id;
//                    $devices->name = Input::get('name');
//                    $devices->model = Input::get('model');
//                    $devices->os = Input::get('os');
//                    $devices->version = Input::get('version');
//                    $devices->udid = Input::get('udid');
//                    $devices->token = Input::get('token');
//                    $devices->identifier = Input::get('identifier');
//                    $devices->status = Input::get('status', 0);
//
//                    $result = $devices->save();
//
//                    if($result){ 
//                        $arrResp['header']['code'] = 200;
//                        $arrResp['header']['message'] = 'Success';
//                        $arrResp['id'] = $devices->id;                
//                        return Response::json($arrResp);
//                    } 
//                    else {    
//                        $arrResp['header']['code'] = 204;
//                        $arrResp['header']['message'] = 'Cannot insert data';
//                        return Response::json($arrResp);
//                    }  
//                }
//            }else{
//                // update device
//
//                $devices = Device::find($id);
//
//                $name = Input::get('name', $devices->name);
//                $model = Input::get('model', $devices->model);
//                $os = Input::get('os', $devices->os);
//                $version = Input::get('version', $devices->version);
//                $udid = Input::get('udid', $devices->udid);
//                $token = Input::get('token', $devices->token);
//                $identifier = Input::get('identifier', $devices->identifier);
//                $status = Input::get('status', $devices->status);
//
//                if($devices){
//                    $result = $devices->where('member_id', '=', $id)->update(array(
//                        'name' => $name,
//                        'model' => $model,
//                        'os' => $os,
//                        'version' => $version,
//                        'udid' => $udid,
//                        'token' => $token,
//                        'identifier' => $identifier,
//                        'status' => $status,
//                    ));
//
//                    if($result)
//                        return Response::json(array(
//                            'header'=> array(
//                                'code'=>200, 
//                                'message'=>'success'
//                            ),
//                            'id'=> $devices->id
//                        ));
//                    else
//                        return 'some error';
//                } else {
//                    return Response::json(array(
//                        'code'=>204, 
//                        'message'=>'no device found to update'
//                    ));
//                }
//                
//            }            
//            
//        }
        
        public function requestOTP(){
            // service P'Roj
        }
        
        public function verifyOTP(){
            // service P'Roj
        }
}