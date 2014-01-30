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
    
    public function requestOTP($id){
        $app_id = '28'; $secret = '018c4fab3425e25bddcf'; $project = 'tms';
        
        $members = Member::find($id);
        
        $url = 'http://widget3.truelife.com/msisdn_service/rest/?method=request_otp&project='.$project.'&app_id='.$app_id.'&secret='.$secret.'&type=register&msisdn='.$members->mobile.'&channel=web';
        return $this->curl_get($url);
    }

    public function verifyOTP($id, $otp){
        $app_id = '28'; $secret = '018c4fab3425e25bddcf'; $project = 'tms';
        
        $members = Member::find($id);
        
        $url = 'http://widget3.truelife.com/msisdn_service/rest/?method=validate_otp&project='.$project.'&app_id='.$app_id.'&secret='.$secret.'&type=register&msisdn='.$members->mobile.'&otp='.$otp.'&ln=en';
        return $this->curl_get($url);
    }
    
    /** $method          get/post
    * $postData        array / xml / json
    * $returnHeader    return http header array http_header(code,content_type,connect_time,total_time) */
    function curl($url,$method='get',$postData="",$isPostXML=false,&$httpHeader=array(),$connectTimeOut=0,$waitTimeOut=0,$custom_header=array(),$isSSL=false,$sslVerifyPeer=false) {
        $header = &$httpHeader;
        $url = trim($url);
        $method=(strtolower($method)=='get')?'get':'post';
        $postFlag = (substr($method,0,1)=='p')?1:0;

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        if ($isPostXML) curl_setopt($c, CURLOPT_HTTPHEADER, array('content-type: text/xml') );
        else if (!empty($custom_header)) curl_setopt($c, CURLOPT_HTTPHEADER, $custom_header);

        //curl_setopt($c, CURLOPT_HEADER, 0);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $connectTimeOut);
        curl_setopt($c, CURLOPT_TIMEOUT, $waitTimeOut);
        curl_setopt($c, CURLOPT_MAXREDIRS, 5);
        curl_setopt($c, CURLOPT_POST, $postFlag);
        if ($isSSL) curl_setopt($c, CURLOPT_SSL_VERIFYPEER, $sslVerifyPeer);
        if ( !empty($postData)) curl_setopt($c, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($c);
//        $header[code] = curl_getinfo($c, CURLINFO_HTTP_CODE);
//        $header[content_type] = curl_getinfo($c, CURLINFO_CONTENT_TYPE);
//        $header[connect_time] = curl_getinfo($c, CURLINFO_CONNECT_TIME);
//        $header[total_time] = curl_getinfo($c, CURLINFO_TOTAL_TIME);
        curl_close($c);

        return $response;
    }
    
    function curl_get($url,$timeOut=0) { return $this->curl($url,'get','',false,$header,0,$timeOut); }
    function curl_get_with_header($url,&$header,$timeOut=0) { return $this->curl($url,'get','',false,$header,0,$timeOut); }
    function curl_post($url,$params,$timeOut=0) { return $this->curl($url,'post',$params,false,array(),0,$timeOut); }
    function curl_post_with_header($url,$params,&$header,$timeOut=0) { return $this->curl($url,'post',$params,false,$header,0,$timeOut); }
    function curl_xml($url,$xml,$timeOut=0) { return $this->curl($url,'post',$xml,true,$header,0,$timeOut); }
    function curl_xml_with_header($url,$xmlRequest,&$header,$timeOut=0) { return $this->curl($url,'post',$xml,true,$header,0,$timeOut); }
}