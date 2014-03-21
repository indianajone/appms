<?php
namespace Max\User\Controllers;

use Validator, Input, Response, Hash, View, Redirect, Auth, App;
use Carbon\Carbon;
use Max\User\Repository\UserRepositoryInterface;
use Indianajone\RolesAndPermissions\RoleRepositoryInterface;

class UserController extends \BaseController 
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
     * @param string $name
	 * @return Response
	 */
	public function index()
	{
        $user = Auth::user();
        
        $users = $this->users->findMany($user->getChildrenId());

        return View::make('users.index',  array('users' => $users));       
	}

    /**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $user = Auth::user();
        $parent = $this->users->findMany($user->getChildrenId(), array('id', 'username'));
        $roles = $this->roles->all();
        
        return View::make('users.create')->with(array(
            'user' => $user,
            'parents' => $parent,
            'roles' => $roles
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
        $user = $this->users->find($id);
        if($user)
        {
            if(Auth::user()->can('edit_user'))
            {
                $root = $this->users->find($user->getRootId());
                $parent = $this->users->findMany($root->getChildrenId(), array('id', 'username'));
                $roles = $this->roles->all();

                return View::make('users.edit')->with(array(
                    'user'=> $user, 
                    'parents'=> $parent,
                    'roles' => $roles
                ));
            }

            return View::make('users.edit')->with('message', 'Permission denied');
        }

        return App::abort(404);
	}

    /**
     * Logout.
     * 
     * @return Response
     */  
    public function doLogout()
    {
        Auth::logout();
        return Redirect::to('v1/users');
    }
}