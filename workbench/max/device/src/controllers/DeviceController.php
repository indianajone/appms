<?php
namespace Max\Device\Controllers;

use Validator, Input, Response, Device;

class DeviceController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
                //
        }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id)
	{
            $rules = array(
                'name'  => 'required',
                'udid'  => 'required',
                'token' => 'required',
                'identifier' => 'required'
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
                $members = Member::select('app_id')->where('id', '=', $id)->get();
                foreach ($members as $member) {
                    $app_id = $member->app_id;
                }

                $devices = new Device();     
                $devices->member_id = $id;
                $devices->app_id = $app_id;
                $devices->name = Input::get('name');
                $devices->model = Input::get('model');
                $devices->os = Input::get('os');
                $devices->version = Input::get('version');
                $devices->udid = Input::get('udid');
                $devices->token = Input::get('token');
                $devices->identifier = Input::get('identifier');
                $devices->status = Input::get('status', 0);

                $result = $devices->save();

                if($result){ 
                    $arrResp['header']['code'] = 200;
                    $arrResp['header']['message'] = 'Success';
                    $arrResp['id'] = $devices->id;                
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
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
            $devices = Device::find($id);

            $name = Input::get('name', $devices->name);
            $model = Input::get('model', $devices->model);
            $os = Input::get('os', $devices->os);
            $version = Input::get('version', $devices->version);
            $udid = Input::get('udid', $devices->udid);
            $token = Input::get('token', $devices->token);
            $identifier = Input::get('identifier', $devices->identifier);
            $status = Input::get('status', $devices->status);
            
            if($devices){
                $result = $devices->where('member_id', '=', $id)->update(array(
                    'name' => $name,
                    'model' => $model,
                    'os' => $os,
                    'version' => $version,
                    'udid' => $udid,
                    'token' => $token,
                    'identifier' => $identifier,
                    'status' => $status,
                ));

                if($result)
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        ),
                        'id'=> $devices->id
                    ));
                else
                    return 'some error';
            } else {
                return Response::json(array(
                    'code'=>204, 
                    'message'=>'no device found to update'
                ));
            }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
            $device = Device::find($id);
            if($device) $device->delete();
	}

}