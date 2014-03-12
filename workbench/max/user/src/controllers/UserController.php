<?php
namespace Max\User\Controllers;

use Validator, Input, Response, Hash, View, Request, Auth;
use Carbon\Carbon;
use Max\User\Models\User;
use Indianajone\RolesAndPermissions\Role;
use Indianajone\RolesAndPermissions\Permission;
use Max\User\Repository\UserRepositoryInterface;

class UserController extends \BaseController 
{
    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $user = Auth::user();
        
        $users = $this->users->findMany($user->getChildrenId());

        return \View::make('users.index')->with('users', $users);


        // API
        // $users = User::whereId($user->id)->apiFilter()->with('children','roles')->get();
       
        // $users->each(function($user){
        //     $user->fields();
        // });

        // dd(\DB::getQueryLog());
      
        // return Response::result(
        //     array(
        //         'header'=> array(
        //             'code'=> 200,
        //             'message'=> 'success'
        //         ),
        //         'offset' => (int) Input::get('offset', 0),
        //         'limit' => (int) Input::get('limit', 10),
        //         'total' => (int) User::tree()->count(),
        //         'entries' => $users->toArray()
        //     )
        // ); 
           
	}

    /**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return $this->store();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $validator = Validator::make(Input::all(), User::$rules['create']);

        if ($validator->passes())
        {
            $user = User::create(
                array(
                    'parent_id'     => Input::get('parent_id'),
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
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
       
        $user = User::apiFilter()->with('apps', 'roles')->whereId($id)->first();
        
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
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $user = User::find($id);
        if($user)
        {
            // if($user->can('create_user'))
                $root = User::find($user->getRootId());
                $parent = User::findMany($root->getChildrenId(), array('id', 'username'));
                $roles = Role::all();

                return View::make('users.edit')->with(array(
                    'user'=> $user, 
                    'parents'=> $parent,
                    'roles' => $roles
                ));

            // return View::make('users.edit')->with('message', 'Permission denied');
        }

       return Response::message(400, 'Invalid selected user.');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$validator = Validator::make(Input::all(), User::$rules['update']);

        if($validator->passes())
        {
            $user = User::find($id);
            if($user)
            {
                $inputs = Input::only('parent_id', 'username', 'first_name', 'last_name', 'email', 'gender', 'birthday');

                foreach ($inputs as $key => $val) {
                    if( $val == null || 
                        $val == '' || 
                        $val == $user[$key]) 
                    {
                        unset($inputs[$key]);
                    }
                }

                if(!count($inputs))
                    return Response::message(204, 'Nothing is update.');

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

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{

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
        Auth::logout();
        
            // return Response::message(200, 'success');
        return \Redirect::to('v1/users');
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
}