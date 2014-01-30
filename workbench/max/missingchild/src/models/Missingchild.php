<?php
namespace Max\Missingchild\Models;

//use Collection;

//use Max\Member\Models\Member;

class Missingchild extends \BaseModel { 
    protected $table = 'missingchilds';
    
    protected $hidden = array('member_id', 'user_id');
    protected $guarded = array('id', 'member_id', 'user_id');

    public static $rules = array(
        'show' => array(
            'id' => 'required|exists:missingchilds'
        ),
        'create' => array(
            // == member require ==
            'username'  => 'required|unique:members,username',
            'password'  => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:members,email',
            'type' => 'required',
            // == missingchild require ==
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
        ),
        'update' => array(
            'id' => 'required|exists:missingchilds',
            'email' => 'required|email|exists:members,email'
        ),
        'delete' => array(
            'id' => 'required|exists:missingchilds'
        ),
        'fields' => array(
            'table' => 'required'
        )
    );
    
    protected function getDateFormat()
    {
        return 'U';
    }
    
    public function user(){
        return $this->belongsTo('User', 'user_id', 'id');
    }
    
    public function member(){
        return $this->hasOne('Max\Member\Models\Member', 'id', 'member_id');
    }
    
    public function newCollection(array $models = array())
    {
        return new Collection($models);
    }
}
?>