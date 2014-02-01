<?php
namespace Max\User\Controllers;

use Validator, Input, Response, Hash;
<<<<<<< HEAD
use Max\User\Models\User;
=======
use Carbon\Carbon;
use Max\User\Models\User;
use Indianajone\RolesAndPermissions\Role;
>>>>>>> best

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
<<<<<<< HEAD
            $offset = Input::get('offset', 0);
            $limit = Input::get('limit', 10);
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : $field;
            $users = User::with('roles','apps')->take($limit)->skip($offset)->get($fields);
            return $users;
            // foreach ($users as $user) {
            //     // $user->apps;
            //     $user->with('roles', 'apps')->get();
            // }
            
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
=======
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = $field ? explode(',', $field) : $field;
        
        $updated_at = Input::get('updated_at', null);
        $created_at = Input::get('created_at', null);

        $users = User::with('apps', 'roles');
        if($updated_at || $created_at)
        {
            if($updated_at) $users = $users->time('updated_at');
            else $users = $users->time('created_at');
        }
        
        $users = $users->offset($offset)->limit($limit)->get();

        if($field)
        $users->each(function($user) use ($fields){
            $user->setVisible($fields);  
        });

        return Response::listing(
            array(
                'code'=>200,
                'message'=> 'success'
            ),
            $users, $offset, $limit
        );
           
>>>>>>> best
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
<<<<<<< HEAD
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
=======
        return $this->store();
>>>>>>> best
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
<<<<<<< HEAD
            //
=======
        $validator = Validator::make(Input::all(), User::$rules['create']);

        if ($validator->passes())
        {
            $user = User::create(
                array(
                    'parent_id'     => Input::get('parent_id', 0),
                    'username'      => Input::get('username'),
                    'password'      => Hash::make(Input::get('password')),
                    'first_name'    => Input::get('first_name'),
                    'last_name'     => Input::get('last_name'),
                    'email'         => Input::get('email'),
                    'gender'        => Input::get('gender'),
                    'birthday'      => Input::get('birthday'),
                    'last_seen'     => Carbon::now()->timestamp
                )
            );

            if($user)
                return Response::result(array(
                    'header' => array(
                        'code'      => 200,
                        'message'   => 'success'
                    ),
                    'id' => $user->id
                ));

            return Response::message(500, 'Something wrong when trying to save user.');
        }
        
        return Response::message(204, $validator->messages()->first());
>>>>>>> best
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
<<<<<<< HEAD
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
=======
        $field = Input::get('fields', null);
        $fields = explode(',', $field);
        $user = User::with('apps', 'roles')->find($id);
        
        if($user)
        {
            return Response::result(
                array(
                    'header' => array(
                        'code' => 200,
                        'message' => 'success'
                    ),
                    'entry' => $user->toArray()
                )
            );
        }

        return Response::message(204, 'User id: '. $id .' does not exists.'); 
>>>>>>> best
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
<<<<<<< HEAD
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
=======
        return $this->update($id);
>>>>>>> best
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
<<<<<<< HEAD
		//
	}

=======
		$validator = Validator::make(Input::all(), User::$rules['update']);

        if($validator->passes())
        {
            $user = User::find($id);
            if($user)
            {
                $inputs = Input::only('parent_id', 'username', 'first_name', 'last_name', 'email', 'gender', 'birthday'
                );

                foreach ($inputs as $key => $val) {
                    if( $val == null || 
                        $val == '' || 
                        $val == $user[$key]) 
                    {
                        unset($inputs[$key]);
                    }
                }

                if(!count($inputs))
                    return Response::message(200, 'Nothing is update.');

                if($user->update($inputs))
                    return Response::message(200, 'Updated user id: '.$id.' success!'); 

                return Response::message(500, 'Something wrong when trying to update user.');;
            }
            return Response::message(204, 'User id: '. $id .' does not exists.'); 
        }

        return Response::message(400, $validator->messages()->first()); 
	}

    public function delete($id)
    {
        return $this->destroy($id);
    }

>>>>>>> best
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
<<<<<<< HEAD
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

        public function attachRole($id)
        {
            $user = User::find($id);
            // dd($user->attachRole(1));
        }
=======

        $validator = Validator::make(array('id'=>$id), User::$rules['delete']);

        if($validator->passes())
        {
            $user = User::find($id)->delete();
            return Response::message(200, 'Deleted User: '.$id.' success!');
        }

        return Response::message(400, $validator->messages()->first()); 
	}
        
    public function doLogin()
    {
        $validator = Validator::make(Input::all(), User::$rules['login']);

        if($validator->passes())
        {
            $userdata = Input::only('username', 'password');

            if(\Auth::attempt($userdata))
                return Response::message(200, 'success');

            return Response::message(204, 'Username or Password is incorrect');
        }

        return Response::message(400, $validator->messages()->first()); 
    }
       
    public function doLogout()
    {
        \Auth::logout();
        return Response::message(200, 'success');
    }
        
    public function resetPassword($id)
    {
        $validator = Validator::make(Input::only('username', 'password', 'new_password'), User::$rules['resetPwd']);

        if($validator->passes())
        {
            $user = User::find($id);

            if($user->checkPassword(Input::get('password')))
            {
                $user->update(array(
                    'password' => Hash::make(Input::get('new_password'))
                ));

                return Response::message(200, 'Update password for User id: '. $id . ' Success!');
            }

            return Response::message(204, 'Username or Password is incorrect');
        }

        return Response::message(400, $validator->messages()->first()); 
    }

    public function manageRole($id, $action)
    {
        $inputs = array_merge(array('id'=> $id, 'action' => $action), Input::only('role_id'));

        Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new \Indianajone\Validators\Rules\ExistLoop($translator, $data, $rules, $messages);
        });

        $validator = Validator::make($inputs, User::$rules['manage_role']);

        if($validator->passes())
        {
            $role = Input::get('role_id');
            $ids = array_flatten(explode(',', $role));
            $user = User::find($id);
            $names = array();

            foreach($ids as $role_id)
            {
                $role = Role::find($role_id);

                if(!$role) 
                    return Response::message(204, 'Role id: '. $id .' does not exists.');

                if($action == 'attach' && !$user->hasRole($role->name))
                {
                    $user->attachRole($role->id);
                    $names[] = $role->name;
                }   

                else if($action == 'detach' && $user->hasRole($role->name))
                {
                    $user->detachRole($role->id);
                    $names[] = $role->name;
                }

            }

            if(!count($names))
                return Response::message(204, $user->username.' is already has roles ' .$action.'ed.');

            return Response::message(200, $user->username.' is now has roles '. implode(', ', $names).' '.$action. 'ed.');
        }

        return Response::message(400, $validator->messages()->first()); 
    }
>>>>>>> best
}