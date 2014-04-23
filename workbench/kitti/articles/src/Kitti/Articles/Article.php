<?php namespace Kitti\Articles;

class Article extends \Eloquent implements \ApiFilterableInteface
{ 
    protected $table = 'articles';
    protected $fillable = array('app_id', 'pre_title', 'title', 'picture', 'teaser', 'content', 'wrote_by', 'published_at', 'views', 'tags');
    protected $hidden = array('app_id', 'status', 'pivot', 'gallery_id', 'deleted_at');

    protected $rules = array(
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

    public function __construct($attributes=array())
    {      
        parent::__construct($attributes);
        $this->setMultiple(array_add($this->getMultiple(), null, 'filterCats'));
        $this->setMap(array_add($this->getMap(), 'filterCats', 'category_id'));
        $this->setDefaults(array_add($this->getDefaults(), 'category_id', '*'));
    }

    use \BaseModel;

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