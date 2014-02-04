<?php namespace Kitti\Articles;

class Article extends \BaseModel
{
    protected $table = 'articles';
    protected $guarded = array('id');
    protected $hidden = array('app_id', 'status', 'pivot');

    public static $rules = array(
    	'show' => array(
    		'appkey' => 'required|exists:applications,appkey'
    	),
        'show_with_id' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id'    => 'required|exists:articles'
        ),
    	'create' => array(
    		'appkey' => 'required|exists:applications,appkey',
            'title' => 'required',
            'content' => 'required',
            'wrote_by' => 'required',
    		'category_id' => 'required|existloop:categories,id',
            'gallery_id' => 'exists:galleries,id'
    	),
        'update' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id'    => 'required|exists:articles',
            'category_id' => 'existloop:categories,id',
            'gallery_id' => 'exists:galleries,id'
        ),
        'delete' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id'    => 'required|exists:articles'
        )
    );

    public function categories()
    {
    	return $this->belongsToMany('Indianajone\\Categories\\Category', 'article_category');
    }

    public function gallery()
    {
        return $this->hasOne('Kitti\\Galleries\\Gallery', 'content_id')->where('content_type', '=', 'article');
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

    public function detachCategory($category)
    {
        if(is_object($category))
            $category = $category->getKey();
        if(is_array($category))
            $category = $category['id'];

        $this->categories()->detach($category);
    }

    public function detachCategories($categories)
    {
        foreach ($categories as $category) {
            $this->detachCategory($category);
        }
    }

    public function getCategoryIds()
    {
       return  $this->categories()->get(array('category_id'))->toArray();
    }
}