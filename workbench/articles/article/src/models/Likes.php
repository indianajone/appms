<?php namespace Articles\Article\Models;

class Likes extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'likes';
        protected $primaryKey = "like_id";
        //public $timestamps = false;
}