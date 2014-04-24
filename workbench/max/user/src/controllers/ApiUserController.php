<?php
namespace Max\User\Controllers;

use Appl, Auth, Hash, Input, Response;
use Carbon\Carbon;
use Max\User\Repository\UserRepositoryInterface;
use Indianajone\RolesAndPermissions\RoleRepositoryInterface;

class ApiUserController extends \BaseController 
{
    public function __construct(
        UserRepositoryInterface $users,
        RoleRepositoryInterface $roles)
    {
        parent::__construct();
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
        $id = $this->users->getIDByToken(Auth::user() ? Auth::user()->getRememberToken() : Input::get('token'));

        if($this->users->validate('index'))
        {
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
                        'total' => (int) is_null($id) ? count($this->users->count()) : $this->users->countChildren($id),
                        'entries' => $users
                    )
                ); 
            }

            return Response::message(204, 'Can not find any users');   
        }

        return Response::message(400, $this->users->errors());
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
        if ($this->users->validate('create'))
        {
            $id = $this->users->create(
                array(
                    'parent_id'     => Input::get('parent_id'),
                    'username'      => Input::get('username'),
                    'password'      => Hash::make(Input::get('password')),
                    'display_name'  => Input::get('display_name', Input::get('username')),
                    'first_name'    => Input::get('first_name'),
                    'last_name'     => Input::get('last_name'),
                    'email'         => Input::get('email')
                )
            );

            if($id)
                return Response::result(array(
                    'header' => array(
                        'code'      => 200,
                        'message'   => 'success'
                    ),
                    'id' => $id
                ));

            return Response::message(500, 'Something wrong when trying to save user.');
        }
        
        return Response::message(204, $this->users->errors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $input = array_add(Input::all(), 'id', $id);
        if($this->users->validate('show', $input))
        {
            return Response::result(
                array(
                    'header' => array(
                        'code' => 200,
                        'message' => 'success'
                    ),
                    'entry' => $this->users->findWith($id)
                )
            );
        }

        return Response::message(400, $this->users->errors());  
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
		$input = array_add(Input::all(), 'id', $id);

        if($this->users->validate('update', $input))
        {
            $input = Input::only('parent_id', 'username', 'first_name', 'last_name', 'email');
            $user = $this->users->update($id, $input);

            if($user)
                return Response::message(200, 'Updated user id: '.$id.' success!'); 

            return Response::message(500, 'Something wrong when trying to update user.');
           
        }

        return Response::message(400, $this->users->errors()); 
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
        $input = array_add(Input::all(), 'id', $id);
        if( $this->users->validate('delete', $input))
        {
            $user = $this->users->delete($id);
            return Response::message(200, 'Deleted User: '.$id.' success!');
        }

        return Response::message(400, $this->users->errors()); 
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
            $credential = Input::only('username', 'password');

            if(Auth::attempt($credential))
            {
                $this->users->updateMeta(Auth::user()->id, array(
                    'last_seen' => Carbon::now()->timestamp
                ));
                
                return Response::message(200, 'success');
            }   
            return Response::message(204, 'Username or Password is incorrect');
        }

        return Response::message(400, $this->users->errors()); 
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

        return Response::message(400, $this->users->errors()); 
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

        return Response::message(400, $this->users->errors()); 
    }
}