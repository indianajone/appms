<?php namespace Galleries\Gallery\Models;

class Likes extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'likes';
        protected $primaryKey = "like_id";
        //public $timestamps = false;
        
        public function entries() {
            return $this->hasOne('Galleries\Gallery\Models\Members','member_id','member_id');
        }
}