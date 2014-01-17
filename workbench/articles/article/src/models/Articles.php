<?php namespace Articles\Article\Models;

class Articles extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'articles';
        protected $primaryKey = "articles_id";
        public $timestamps = false;
}