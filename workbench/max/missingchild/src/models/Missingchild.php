<?php
namespace Max\Missingchild\Models;

class Missingchild extends \Eloquent {
 
    protected $table = 'missingchilds';
    
    public function user(){
        return $this->hasOne('User', 'id', 'user_id');
    }
    
    public function member(){
        return $this->hasOne('Member', 'id', 'member_id');
    }
}
?>