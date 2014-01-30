<?php
namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Appl, Hash;
use Carbon\Carbon;
use Max\Missingchild\Models\Missingchild;
use Max\Member\Models\Member;
//use Indianajone\Applications\Application;
//
//use Max\Missingchild\Models\Collection;

class MissingchildController extends \BaseController {

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
            
            $updated_at = Input::get('updated_at', null);
            $created_at = Input::get('created_at', null);
            
            $mcs = Missingchild::with('member');
            
            if($updated_at || $created_at)
            {
                if($updated_at) $mcs = $mcs->time('updated_at');
                else $mcs = $mcs->time('created_at');
            }
            
            $mcs = $mcs->offset($offset)->limit($limit)->get();
            
            if($field)
                $mcs->each(function($mc) use ($fields){
                    $mc->setVisible($fields);  
                });

                return Response::listing(
                    array(
                        'code'=>200,
                        'message'=> 'success'
                    ),
                    $mcs->members(), $offset, $limit
                );
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
            $validator = Validator::make(Input::all(), Missingchild::$rules['create']);
            
            if ($validator->fails()) {                
                return Response::message(204, $validator->messages()->first());               
            }else{                                
                $member = new Member();
                $member->app_id = Appl::getAppIDByKey(Input::get('appkey'))->id;
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
//                $member->verified = Input::get('verified', 0);
                $member->email = Input::get('email');
                $member->address = Input::get('address');            
                $member->gender = Input::get('gender');
                $member->birthday = Input::get('birthday');       
                $member->description = Input::get('description');    
                $member->type = Input::get('type');
                $member->last_seen = Carbon::now()->timestamp;
                $member->status = 1;
                
                if($member->save()){
                    $mcs = new Missingchild();
                    $mcs->member_id = $member->id;
                    $mcs->user_id = Input::get('user_id');
                    $mcs->place_of_missing = Input::get('place_of_missing');
                    $mcs->place_of_report = Input::get('place_of_report');
                    $mcs->reporter = Input::get('reporter');
                    $mcs->relationship = Input::get('relationship');
                    $mcs->note = Input::get('note');
                    $mcs->approved = Input::get('approved');
                    $mcs->follow = Input::get('follow');
                    $mcs->founded = Input::get('founded');
                    $mcs->public = Input::get('public');
                    $mcs->order = Input::get('order');
                    $mcs->missing_date = Input::get('missing_date');
                    $mcs->report_date = Input::get('report_date');
                    $mcs->status = 1;

                    if($mcs->save()){ 
                        return Response::result(array(
                            'header' => array(
                                'code'      => 200,
                                'message'   => 'success'
                            ),
                            'id' => $mcs->id
                        ));
                    } 
                    else {    
                        $member = Member::find($member->id)->delete();
                        return Response::message(500, 'Something wrong when trying to save missingchild.');
                    }             
                }else{
                    return Response::message(500, 'Something wrong when trying to save member.');
                }
            }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $validator = Validator::make(array('id' => $id), Missingchild::$rules['show']);
            
            if ($validator->fails())
                return Response::message(204, $validator->messages()->first());
            else{        
                $field = Input::get('fields');
                $fields = $field ? explode(',', $field) : '*';
                $mcs = Missingchild::with('member')->select($fields)->where('id', '=', $id)->get();
                
                return Response::json(array(
                    'header' => array(
                        'code' => 200, 
                        'message' => 'success'
                    ),
                    'entries'=> [$mcs->members()->toArray()]
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
            $req = array_merge(array('id' => $id), Input::all());
            $validator = Validator::make($req, array(
                'id' => 'required|exists:missingchilds',
                'email' => 'required|email|exists:members,email,id,'.Missingchild::find($id)->member_id
                ));
            
            if ($validator->fails()) {
                return Response::message(204, $validator->messages()->first());                
            }else{
                $mcs = Missingchild::find($id);
                
//                foreach ($req as $key => $val) {
//                    if( $val == null || 
//                        $val == '' || 
//                        $val == $mcs[$key]) 
//                    {
//                        unset($req[$key]);
//                    }
//                }
//
//                if(!count($req))
//                    return Response::message(200, 'Nothing is update.');
                
                $input = array(
//                    'member_id' => Input::get('member_id', $mcs->member_id),
//                    'user_id' => Input::get('user_id', $mcs->user_id),
                    'place_of_missing' => Input::get('place_of_missing', $mcs->place_of_missing),
                    'place_of_report' => Input::get('place_of_report', $mcs->place_of_report),
                    'reporter' => Input::get('reporter', $mcs->reporter),
                    'relationship' => Input::get('relationship', $mcs->relationship),
                    'note' => Input::get('note', $mcs->note),
                    'approved' => Input::get('approved', $mcs->approved),
                    'follow' => Input::get('follow', $mcs->follow),
                    'founded' => Input::get('founded', $mcs->founded),
                    'public' => Input::get('public', $mcs->public),
                    'order' => Input::get('order', $mcs->order),
                    'missing_date' => Input::get('missing_date', $mcs->missing_date),
                    'report_date' => Input::get('report_date', $mcs->report_date),
                    'status' => Input::get('status', $mcs->status),
                );
                
                $member = Member::find($mcs->member_id);
                $input_member = array(
                    'parent_id' => Input::get('parent_id', $member->parent_id),
                    'fbid' => Input::get('fbid', $member->fbid),
                    'fbtoken' => Input::get('fbtoken', $member->fbtoken),
                    'username' => Input::get('username', $member->username),
                    'title' => Input::get('title', $member->title),
                    'first_name' => Input::get('first_name', $member->first_name),
                    'last_name' => Input::get('last_name', $member->last_name),
                    'other_name' => Input::get('other_name', $member->other_name),
                    'phone' => Input::get('phone', $member->phone),
                    'mobile' => Input::get('mobile', $member->mobile),
                    'email' => Input::get('email', $member->email),
                    'address' => Input::get('address', $member->address),            
                    'gender' => Input::get('gender', $member->gender),
                    'birthday' => Input::get('birthday', $member->birthday),
                    'description' => Input::get('description', $member->description),    
                    'type' => Input::get('type', $member->type),
                    'status' => Input::get('status', $member->status)
                );
                
                if($mcs->where('id', '=', $id)->update($input)){
                    if($member->where('id', '=', $mcs->member_id)->update($input_member))
                        return Response::message(200, 'Updated missingchild id: '.$id.' success!');
                    else
                        return Response::message(500, 'Something wrong when trying to update missingchild.');                    
                }else{
                    return Response::message(500, 'Something wrong when trying to update missingchild.');
                }
            }
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
            $validator = Validator::make(array('id' => $id), Missingchild::$rules['delete']);
            
            if ($validator->fails()) {
                return Response::message(400, $validator->messages()->first());           
            }else{      
                $mcs = Missingchild::find($id);
                $members = Member::find($$mcs->member_id);
                
                if($mcs->delete() && $members->delete()){
                    return Response::message(200, 'Deleted Missingchild: '.$id.' success!');
                }else{
                    return Response::message(500, 'Something wrong when trying to delete missingchild.');    
                }
            }
	}
}