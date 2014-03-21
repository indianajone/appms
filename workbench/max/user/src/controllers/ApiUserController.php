<?php
namespace Max\User\Controllers;

use Validator, Input, Response, Hash, View, Request, Auth;
use Carbon\Carbon;
use Max\User\Repository\UserRepositoryInterface;
use Indianajone\RolesAndPermissions\RoleRepositoryInterface;

class ApiUserController extends \BaseController 
{
    public function __construct(
        UserRepositoryInterface $users,
        RoleRepositoryInterface $roles)
    {
        $this->users = $users;
        $this->roles = $roles;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $id = Input::get('user_id');

        $users = !is_null($id) ? 
                    $this->users->findUserAndChildren($id) :
                    $this->users->all();

        if($users)
        {
            return Response::result(
                array(
                    'header'=> array(
                        'code'=> 200,
                        'message'=> 'success'
                    ),
                    'offset' => (int) Input::get('offset', 0),
                    'limit' => (int) Input::get('limit', 10),
                    'total' => (int) $this->users->countChildren($id) ?: count($users),
                    'entries' => $users
                )
            ); 
        }

        return Response::message(204, 'Can not find any users');   
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
        $validator = $this->users->validate('create');

        if ($validator)
        {
            $user = $this->users->create(
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
        
        return Response::message(204, $this->users->errors);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $user = $this->users->findWith($id, array('apps', 'roles'));

        return Response::result(
            array(
                'header' => array(
                    'code' => 200,
                    'message' => 'success'
                ),
                'entry' => $user
            )
        );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return $this->update($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$validator = $this->users->validate('update', array('id'=>$id));

        if($validator)
        {
            $user = $this->users->find($id);
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

            return Response::message(500, 'Something wrong when trying to update user.');
           
        }

        return Response::message(400, $this->users->errors); 
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

        $validator = $validator = $this->users->validate('delete');

        if($validator)
        {
            $user = $this->users->delete($id);
            return Response::message(200, 'Deleted User: '.$id.' success!');
        }

        return Response::message(400, $this->users->errors); 
	}

    /**
     * Login user via API.
     * 
     * @return Response
     */    
    public function doLogin()
    {
        $validator = $this->users->validate('login');

        if($validator)
        {
            $userdata = Input::only('username', 'password');

            if(Auth::attempt($userdata))
            {
                $user = Auth::user();
                $user->timestamps = false;
                $user->update(array(
                    'last_seen' => Carbon::now()->timestamp
                ));
                
                return Response::message(200, 'success');
            }   
            return Response::message(204, 'Username or Password is incorrect');
        }

        return Response::message(400, $this->users->errors); 
    }
    
    /**
     * Logout user via API.
     * 
     * @return Response
     */  
    public function doLogout()
    {
        Auth::logout();
        return Response::message(200, 'success');
    }

    /**
     * Reset password to new one.
     * 
     * @return Response
     */      
    public function resetPassword($id)
    {
        $validator = $this->users->validate('resetPwd');

        if($validator)
        {
            $user = $this->users->find($id);

            if($user->checkPassword(Input::get('password')))
            {
                $this->users->update(array(
                    'password' => Hash::make(Input::get('new_password'))
                ));

                return Response::message(200, 'Update password for User id: '. $id . ' Success!');
            }

            return Response::message(204, 'Username or Password is incorrect');
        }

        return Response::message(400, $this->users->errors); 
    }

    /**
     * Attach or Detach roles.
     * 
     * @param   int $id 
     * @param   string $action 
     * @return  Response
     */
    public function manageRole($id, $action)
    {
        $inputs = array_merge(array('id'=> $id, 'action' => $action), Input::only('role_id'));
        $validator = $this->users->validate('manage_role', $inputs);

        if($validator)
        {
            $role = Input::get('role_id');
            $ids = array_flatten(explode(',', $role));
            $user = $this->users->find($id);
            $names = array();

            foreach($ids as $role_id)
            {
                $role = $this->roles->find($role_id);

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

        return Response::message(400, $this->users->errors); 
    }
}