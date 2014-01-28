<?php
namespace Max\Member\Controllers;

use Validator, Input, Response, Hash, Appl;
use Max\Member\Models\Member;
use Carbon\Carbon;


class MemberController extends \BaseController {

 	public function index()
    {
    	$offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = $field ? explode(',', $field) : $field;
        
        $updated_at = Input::get('updated_at', null);
        $created_at = Input::get('created_at', null);

        $members = Member::query();
        if($updated_at || $created_at)
        {
            if($updated_at) $members = $members->time('updated_at');
            else $members = $members->time('created_at');
        }
        
        $members = $members->active()->offset($offset)->limit($limit)->get();

        if($field)
        $members->each(function($user) use ($fields){
            $user->setVisible($fields);  
        });

        return Response::listing(
            array(
                'code'=>200,
                'message'=> 'success'
            ),
            $members, $offset, $limit
        );
    }

    public function create()
	{
		return $this->store();
	}

	public function store()
    {
 		$validator = Validator::make(Input::all(), Member::$rules['create']);

 		if($validator->passes())
 		{

 			$member = Member::create(
                array(
                    'app_id'        => Appl::getAppIDByKey(Input::get('appkey'))->id,
                    'parent_id'     => Input::get('parent_id', 0),
                    'fbid'          => Input::get('fbid'),
                    'fbtoken'       => Input::get('fbtoken'),
                    'username'      => Input::get('username'),
                    'password'      => Hash::make(Input::get('password')),
                    'title'         => Input::get('title'),
                    'first_name'    => Input::get('first_name'),
                    'last_name'     => Input::get('last_name'),
                    'other_name'    => Input::get('other_name'),
                    'phone'         => Input::get('phone'),
                    'mobile'        => Input::get('mobile'),
                    'email'         => Input::get('email'),
                    'address'       => Input::get('address'),
                    'gender'        => Input::get('gender'),
                    'birthday'      => Input::get('birthday'),
                    'description'   => Input::get('description'),
                    'type'          => Input::get('type'),
                    'last_seen'     => Carbon::now()->timestamp,
                    'status'        => 1
                )
            );

            if($member)
                 return Response::result(array(
                    'header' => array(
                        'code'      => 200,
                        'message'   => 'success'
                    ),
                    'id' => $member->id
                ));

            return Response::message(500, 'Something wrong when trying to save member.');
 		}

 		return Response::message(204, $validator->messages()->first());
    }

    public function show($id)
	{
        $field = Input::get('fields', null);
        $fields = explode(',', $field);	
        $member = Member::find($id);

        if($member)
        {
            return Response::result(
                array(
                    'header' => array(
                        'code' => 200,
                        'message' => 'success'
                    ),
                    'entry' => $member->toArray()
                )
            );
        }

        return Response::message(204, 'Member id: '. $id .' does not exists.');
    }

	public function edit($id)
    {
    	return $this->update($id);
    }

    public function update($id)
    {
        $inputs = array_merge(array('id' => $id), Input::only('parent_id', 'fbid', 'fbtoken', 'username', 'title', 'first_name', 'last_name', 'other_name', 'phone', 'mobile', 'email', 'address', 'gender', 'birthday', 'description', 'status'));
        $validator = Validator::make($inputs, Member::$rules['update']);

        if($validator->passes())
        {
            $member = Member::find($id);
            foreach ($inputs as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $member[$key]) 
                {
                    unset($inputs[$key]);
                }
            }

            if(!count($inputs))
                return Response::message(200, 'Nothing is update.');

            if($member->update($inputs))
                return Response::message(200, 'Updated member id: '.$id.' success!');

            return Response::message(500, 'Something wrong when trying to update member.');
        }

        return Response::message(400, $validator->messages()->first()); 
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
        $validator = Validator::make(array('id'=>$id), Member::$rules['delete']);

        if($validator->passes())
        {
            $member = Member::find($id)->delete();
            return Response::message(200, 'Deleted Member: '.$id.' success!');
        }

        return Response::message(400, $validator->messages()->first()); 
    }

    public function doLogin()
    {
        $validator = Validator::make(Input::all(), Member::$rules['login']);

        if($validator->passes())
        {

            $member = Member::whereUsername(Input::get('username'))->first(array('id', 'password'));
            if($member->checkPassword(Input::get('password')))
            {
                $inputs = array('last_seen' => Carbon::now()->timestamp); 
                if($member->update($inputs))
                    return Response::message(200, 'success');

                return Response::message(500, 'Something wrong when trying to login.');
            }
            return Response::message(400, 'Username or Password is incorrect');
        }

        return Response::message(400, $validator->messages()->first()); 
    }

    public function doLogout()
    {
        return Response::message(200, 'success');
    }

    public function resetPassword($id)
    {
        $validator = Validator::make(Input::only('username', 'password', 'new_password'), Member::$rules['resetPwd']);

        if($validator->passes())
        {
            $user = Member::find($id);

            if($user->checkPassword(Input::get('password')))
            {
                $user->update(array(
                    'password' => Hash::make(Input::get('new_password'))
                ));

                return Response::message(200, 'Update password for Member id: '. $id . ' Success!');
            }

            return Response::message(204, 'Username or Password is incorrect');
        }

        return Response::message(400, $validator->messages()->first()); 
    }

}