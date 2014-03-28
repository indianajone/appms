<?php namespace Kitti\Articles;

use Indianajone\Categories\Category;

class Article extends \Eloquent
{
    protected $table = 'articles';
    protected $guarded = array('id');
    protected $hidden = array('app_id', 'status', 'pivot', 'gallery_id', 'deleted_at');

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
    		'category_id' => 'required|existsloop:categories,id',
            'gallery_id' => 'exists:galleries,id'
    	),
        'update' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id'    => 'required|exists:articles',
            'category_id' => 'existsloop:categories,id',
            'gallery_id' => 'exists:galleries,id'
        ),
        'delete' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id'    => 'required|exists:articles'
        )
    );

    use \BaseModel;

    function __construct()
    {
        parent::__construct();
        
        $this->map = array(
            'order_by' => 'order_by',
            'limit' => 'limit',
            'offset' => 'offset',
            'search' => 'q',
            'filterCats' => 'category_id',
            'whereUpdated' => 'updated_at',
            'whereCreated' => 'created_at'
        );

        $this->multiple = array(
            'filterCats',
            'order_by'
        );
    }

    public function categories()
    {
    	return $this->belongsToMany('Indianajone\\Categories\\Category', 'article_category');
    }

    public function gallery()
    {
        return $this->morphOne('Kitti\\Galleries\\Gallery', 'galleryable', 'content_type', 'content_id');
    }

    public function attachCategory($category)
    {
    	if(is_object($category))
    		$category = $category->getKey();
    	if(is_array($category))
    		$category = $category['id'];

        $cat = $this->categories()->where('category_id', '=', $category)->get();

        if($cat->count() === 0)
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

    public function syncRelations($related, $categories)
    {
        if (is_string($categories))
            $categories = explode(',', $categories);

        $this->{$related}()->sync($categories);
        $this->touch();
    }

    public function getCategoryIds()
    {
       return  $this->categories()->get(array('category_id'))->toArray();
    }
}