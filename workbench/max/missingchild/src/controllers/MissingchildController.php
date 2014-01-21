<?php
namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Max\Missingchild\Models\Missingchild;

class MissingchildController extends \BaseController {

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
            $mcs = Missingchild::take($limit)->skip($offset)->with('member', 'user')->get($fields);
            
            if($mcs->count() > 0)
                return Response::listing(array(
                        'code'=>200, 
                        'message'=>'success'
                ), $mcs, $offset, $limit);
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
                'member_id'  => 'required',
                'user_id'  => 'required',
                'place_of_missing' => 'required',
                'place_of_report' => 'required',
                'reporter' => 'required',
                'relationship' => 'required',
                'note'  => 'required',
                'approved'  => 'required',
                'follow' => 'required',
                'founded' => 'required',
                'public' => 'required',
                'order' => 'required',
                'missing_date'  => 'required',
                'report_date'  => 'required',
                'status' => 'required',
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

                $mcs = new Missingchild();
                $mcs->member_id = Input::get('member_id');
                $mcs->user_id = Input::get('user_id');
                $mcs->place_of_missing = Hash::make(Input::get('place_of_missing'));
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
                
                $result = $mcs->save();

                if($result){ 
                    $arrResp['header']['code'] = 200;
                    $arrResp['header']['message'] = 'Success';
                    $arrResp['id'] = $mcs->id;                
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
            $mcs = Missingchild::with('member', 'user')->select($fields)->where('id', '=', $id)->get();
            
            if($mcs)
                return Response::json(array(
                    'header'=> array(
                        'code'=>200, 
                        'message'=>'success'
                    ),
                    'entries'=> [$mcs->toArray()]
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
            if (empty($id)) {
                $msg = "parameter 'id' required";
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            }
            
            $mcs = Missingchild::find($id);
            
            $member_id = Input::get('member_id', $mcs->member_id);
            $user_id = Input::get('user_id', $mcs->user_id);
            $place_of_missing = Input::get('place_of_missing', $mcs->place_of_missing);
            $place_of_report = Input::get('place_of_report', $mcs->place_of_report);
            $reporter = Input::get('reporter', $mcs->reporter);
            $relationship = Input::get('relationship', $mcs->relationship);
            $note = Input::get('note', $mcs->note);
            $approved = Input::get('approved', $mcs->approved);
            $follow = Input::get('follow', $mcs->follow);
            $founded = Input::get('founded', $mcs->founded);
            $public = Input::get('public', $mcs->public);
            $order = Input::get('order', $mcs->order);
            $missing_date = Input::get('missing_date', $mcs->missing_date);
            $report_date = Input::get('report_date', $mcs->report_date);
            $status = Input::get('status', $mcs->status);
            
            date_default_timezone_set('Asia/Bangkok');
            
            if($mcs){
                $result = $mcs->where('id', '=', $id)->update(array(
                    'member_id' => $member_id,
                    'user_id' => $user_id,
                    'place_of_missing' => $place_of_missing,
                    'place_of_report' => $place_of_report,
                    'reporter' => $reporter,
                    'relationship' => $relationship,
                    'note' => $note,
                    'approved' => $approved,
                    'follow' => $follow,
                    'founded' => $founded,
                    'public' => $public,
                    'order' => $order,
                    'missing_date' => $missing_date,
                    'report_date' => $report_date,
                    'status' => $status,
                ));

                if($result)
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        ),
                        'id'=> $mcs->id
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
            $mcs = Missingchild::find($id);
            if($mcs) $mcs->delete();
	}
        
        public function fields($table, $format='json')
        {
            return Response::fields($table, $format);
        }

}