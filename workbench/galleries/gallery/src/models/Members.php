<?php namespace Galleries\Gallery\Models;

class Members extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'members';
    protected $primaryKey = "id";
        
//        public function entries() {
//            return $this->belongTo('Articles\Article\Models\Likes','member_id');
//        }
        
//        public function entries() {
//            return $this->belongsTo('Galleries\Gallery\Models\Likes');
//        }
}
