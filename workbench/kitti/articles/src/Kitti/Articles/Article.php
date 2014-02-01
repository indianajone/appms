<?php namespace Kitti\Articles;

class Article extends \BaseModel
{
	protected $table = 'articles';
  	protected $guarded = array('id');
    protected $hidden = array('app_id', 'user_id','gallery_id', 'categories_id', 'status','views');

    public static $rules = array(
    	'show' => array(
    		'id' => 'required|exists:articles'
    	),
    	'create' => array(
    		'user_id' => 'required',
    		'title' => 'required',
    		'content' => 'required'
		),
		'like' => array(
			'member_id' => 'required'
		),
	    'delete' => array(
	      'id' => 'required|exists:articles'
	    )
    	// 'show_by_owner' => array(
    	// 	'type' => 'required|in:member,article',
    	// 	'id' => 'required',
    	// ),
    	// 'create' => array(
    	// 	// 'appkey' => 'required',
    	// 	'content_id' => 'required',
    	// 	'content_type' => 'required|in:member,article',
    	// 	'name' => 'required'
    	// )
    );

    public function scopeActive($query)
    {
        return $query->whereStatus(1);
    }

    public function scopeTitle($query,$param)
    {
    	return $query->where('title', 'LIKE',"%".$param."%");
    }

    public function scopeCategories($query, $param)
    {
    	//$cat_id = explode(',',$param);
    	
    	//return $query->where('categories_id','LIKE',"%".$param."%");
    	//return $query->whereIn('id', $param);
    	return $query;
    }

    public function scopeOthers($query,$param) {
    	foreach($param as $key => $value) {
    		$query->where($key,'LIKE',"%".$value."%");
    	}

    	return $query;
    }
}