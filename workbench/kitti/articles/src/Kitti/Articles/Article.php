<?php namespace Kitti\Articles;

class Article extends \BaseModel
{
    protected $table = 'articles';
    protected $guarded = array('id');

    public static $rules = array(
    	'show' => array(
    		'appkey' => 'required|exists:applications,appkey'
    	),
    	'create' => array(
    		'appkey' => 'required|exists:applications,appkey',
    		'gallery_id' => 'exists:galleries',
    		'category_id' => 'existloop:categories,id',
    		'title' => 'required',
    		'content' => 'required',
    		'wrote_by' => 'required'
    	)
    );

    public function categories()
    {
    	return $this->belongsToMany('Indianajone\\Categories\\Category', 'article_category', 'article_id')->withPivot('category_id');
    }

    public function scopeWhereCat($query, $cats)
    {
        $ids = $this->categories()->wherePivot('category_id', '=', 1);
        dd($ids->get());
        dd($query->whereIn('id', $cats)->toSql());
      // return $query->whereHas('article_category', 
    	// return $query->wherehas('categories', function($q) use ($cats){
    	// 	$q->whereIn('article_category.category_id', $cats);
    	// });
    	//->whereIn('article_category.category_id', $cats);
    }

    public function attachCategory($category)
    {
    	if(is_object($category))
    		$category = $category->getKey();
    	if(is_array($category))
    		$category = $category['id'];

    	$this->categories()->attach($category);
    }

    public function attachCategories($categories)
    {
    	foreach ($categories as $category) {
    		$this->attachCategory($category);
    	}
    }
}