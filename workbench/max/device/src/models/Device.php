<?php
 
class Device extends Eloquent {
 
    protected $table = 'devices';
    
    public static $rules = array(
        'create' => array(
            'name'  => 'required',
            'udid'  => 'required',
            'token' => 'required',
            'identifier' => 'required'
        ),
        'update' => array(
            'channel'  => 'required',
            'udid'  => 'required',
            'token' => 'required'
        ),
        'delete' => array(
            'id' => 'required|exists:devices'
        )
    );
    
    protected function getDateFormat()
    {
        return 'U';
    }
    
    public function member(){
        return $this->belongsTo('Member', 'member_id');
    }
}
?>