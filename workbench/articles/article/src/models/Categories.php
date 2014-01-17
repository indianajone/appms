<?php namespace Articles\Article\Models;

class Categories extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'categories';
        protected $primaryKey = "category_id";
        //public $timestamps = false;
        
        public function scopeGetall($query, $array)
        {
            return $query->whereIn('category_id',$array);
        }
        
        public function children() {
            return $this->hasMany('Articles\Article\Models\Categories', 'parent_id','category_id')->with('children');
        }
        
}