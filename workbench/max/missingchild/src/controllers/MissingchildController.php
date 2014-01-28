<?php
namespace Max\Missingchild\Controllers;

use Validator, Input, Response;
use Max\Missingchild\Models\Missingchild;
use Indianajone\Applications\Application;

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
                if($updated_at) $users = $users->time('updated_at');
                else $users = $users->time('created_at');
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
                    $mcs, $offset, $limit
                );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{            
            $validator = Validator::make(Input::all(), Missingchild::$rules['create']);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));                
            }else{                
                $appkey = Input::get('appkey');
                $apps = Application::select('id')->where('appkey', '=', $appkey)->first();
                
                $member = new Member();       
                $member->app_id =  $apps->id;
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
//                $member->otp = rand(0000, 9999);
                $member->verified = Input::get('verified', 0);
                $member->email = Input::get('email');
                $member->address = Input::get('address');            
                $member->gender = Input::get('gender');
                $member->birthday = Input::get('birthday');       
                $member->description = Input::get('description');    
                $member->type = Input::get('type');
                $member->status = Input::get('status', 0);
                
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
                    $mcs->status = Input::get('status');

                    if($mcs->save()){ 
                        $arrResp['header']['code'] = 200;
                        $arrResp['header']['message'] = 'Success';
                        $arrResp['id'] = $mcs->id;                
                        return Response::json($arrResp);
                    } 
                    else {    
                        $member = Member::find($member->id)->delete();
                        $arrResp['header']['code'] = 204;
                        $arrResp['header']['message'] = 'Cannot insert data';
                        return Response::json($arrResp);
                    }             
                }else{
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
            $validator = Validator::make(array('id' => $id), Missingchild::$rules['show']);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));                
            }else{        
                $field = Input::get('fields');
                $fields = $field ? explode(',', $field) : '*';
                $mcs = Missingchild::with('member')->select($fields)->where('id', '=', $id)->get();
                
                return Response::json(array(
                    'header'=> array(
                        'code'=>200, 
                        'message'=>'success'
                    ),
                    'entries'=> [$mcs->toArray()]
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
            $validator = Validator::make(array('id' => $id), Missingchild::$rules['update']);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));                
            }else{                
                $mcs = Missingchild::find($id);
                $input = array(
                    'member_id' => Input::get('member_id', $mcs->member_id),
                    'user_id' => Input::get('user_id', $mcs->user_id),
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
                
                if($mcs->where('id', '=', $id)->update($input))
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        ),
                        'id'=> $mcs->id
                    ));
                else
                    return Response::json(array(
                        'code'=>204, 
                        'message'=>'cannot update data'
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
            $validator = Validator::make(array('id' => $id), Missingchild::$rules['delete']);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));                
            }else{      
                $mcs = Missingchild::find($id);
                
                if($mcs->delete()){
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
            }
	}
        
        public function fields($table = 'missingchilds', $format = 'json')
        {
            $validator = Validator::make(array('table' => $table), Missingchild::$rules['fields']);
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));    
            }else{
                return Response::fields($table, $format);
            }
        }
}