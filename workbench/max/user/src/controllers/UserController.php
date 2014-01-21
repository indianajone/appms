<?php
namespace Max\User\Controllers;

use Validator, Input, Response, Hash, User;

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            $offset = Input::get('offset', 0);
            $limit = Input::get('limit', 10);
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : $field;
            $users = User::take($limit)->skip($offset)->get($fields);
            // return $users;
            foreach ($users as $user) {
                $user->apps;
            }
            
            if($users->count() > 0)
                return $users;
                // return Response::listing(array(
                //     'code'=>200, 
                //     'message'=>'success'
                // ), $users, $offset, $limit);
            else 
                // return Response::listing(array(
                //     'code'=>204, 
                //     'message'=>'no content'
                // ), null, $offset, $limit);
                return 'false';
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            $rules = array(
                'username'  => 'required|unique:users,username',
                'password'  => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
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

                $user = new User();
                $user->parent_id = Input::get('parent_id', 0);
                $user->username = Input::get('username');
                $user->password = Hash::make(Input::get('password'));
                $user->first_name = Input::get('first_name');
                $user->last_name = Input::get('last_name');
                $user->email = Input::get('email');
                $user->gender = Input::get('gender');
                $user->birthday = Input::get('birthday');
                
                $result = $user->save();

                if($result){ 
                    $arrResp['header']['code'] = 200;
                    $arrResp['header']['message'] = 'Success';
                    $arrResp['id'] = $user->id;                
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
            $user = User::with('apps')->select($fields)->where('id', '=', $id)->get();
            
            if(count($user) > 0){
                return Response::json(array(
                    'header'=> array(
                        'code'=>200, 
                        'message'=>'success'
                    ),
                    'entries'=> [$user->toArray()]
                ));
            }else{
                return Response::json(array(
                    'header'=> array(
                        'code'=>204, 
                        'message'=>'no content'
                    )
                ));
            }
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
                'email' => 'required|email|exists:users,email,id,'.$id,
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
            
            $user = User::find($id);
            
            $parent_id = Input::get('parent_id', $user->parent_id);
            $username = Input::get('username', $user->username);
            $password = Input::get('password', $user->password);
            $first_name = Input::get('first_name', $user->first_name);
            $last_name = Input::get('last_name', $user->last_name);         
            $email = Input::get('email', $user->email);
            $gender = Input::get('gender', $user->gender);
            $birthday = Input::get('birthday', $user->birthday);
            
            date_default_timezone_set('Asia/Bangkok');
            
            if($user){
                $result = $user->where('id', '=', $id)->update(array(
                    'parent_id' => $parent_id,
                    'username' => $username,
                    'password' => $password,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'gender' => $gender,
                    'birthday' => $birthday,
                ));

                if($result)
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        ),
                        'id'=> $user->id
                    ));
                else
                    return 'some error';
            } else {
                return Response::json(array(
                    'code'=>204, 
                    'message'=>'no user found to update'
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
            $user = User::find($id);
            if($user){
                if($user->delete()){
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        )
                    ));
                }else{
                    return Response::json(array(
                        'header'=> array(
                            'code'=>204, 
                            'message'=>'cannot delete'
                        )
                    ));
                }                
            }else{
                return Response::json(array(
                        'header'=> array(
                            'code'=>204, 
                            'message'=>'no content'
                        )
                    ));
            }
	}
        
        public function fields()
        {
            $user = new User();
            return $user->getAllColumnsNames();
        }
        
        public function hasParent(){
            $rules = array(
                'user_id' => 'required|exists:users,id',
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
            
            $user_id = Input::get('user_id');
            $parent_id = User::select('parent_id')->find($user_id);
            
            if(count($parent_id->parent_id) > 0){
                return Response::json(array(
                    'header'=> array(
                        'code' => 200, 
                        'message' => 'success'
                    ),
                    'parent_id' => $parent_id->parent_id
                ));
            }else{
                return Response::json(array(
                    'header'=> array(
                        'code' => 204, 
                        'message' => 'no content'
                    )
                ));
            }
            
        }
        
        public function doLogin(){            
            $rules = array(
                'username'  => 'required|exists:users,username',
                'password'  => 'required'
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
                    $userdata = array(
                        'username' 	=> Input::get('username'),
                        'password' 	=> Input::get('password')
                    );
                    
                    if (Auth::attempt($userdata)) {
                        return Response::json(array(
                            'header'=> array(
                                    'code' => 200, 
                                    'message' => 'success'
                            )
                        ));

                    } else {
                        return Response::json(array(
                            'header'=> array(
                                'code' => 204, 
                                'message' => 'username / password incorrect'
                                )
                        ));
                    }
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
                'username'    => 'required|exists:users,username',
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
                $user = User::where('username', '=', $userdata['username']);
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
}