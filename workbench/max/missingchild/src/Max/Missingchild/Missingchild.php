<?php namespace Max\Missingchild\Models;

use Input, Response;
use Max\Missingchild\Collection;
use Indianajone\Categories\Category;

class Missingchild extends \Eloquent
{
	protected $table = 'missingchilds';
    protected $softDelete = true;
    protected $guarded = array('id');
    protected $hidden = array('status', 'app_id', 'gallery_id', 'article_id', 'user_id', 'title', 'categories', 'deleted_at');

    public static $rules = array(
        'show' => array(
            'appkey'    => 'required|exists:applications,appkey'
        ),
        'show_with_id' => array(
            'appkey'    => 'required|exists:applications,appkey',
            'id'        => 'required|existsinapp:missingchilds,id,Max\\Missingchild\\Models\\Missingchild'
        ),
        'create' => array(
            'appkey'            => 'required|exists:applications,appkey',
            'category_id'       => 'required|existsloop:categories,id',
            'title'             => 'required',
            'description'           => 'required',
            'first_name'        => 'required',
            'last_name'         => 'required',
            'lost_age'          => 'required|integer',
            'place_of_missing'  => 'required',
            'missing_at'        => 'required',
            'reported_at'       => 'required',
            'user_id'           => 'exists:users,id',
            'order'             => 'integer'
        ),
        'create_clue' => array(
            'appkey'         => 'required|exists:applications,appkey',
            'id'             => 'required|exists:missingchilds,id',
            'article_id'     => 'required|exists:articles,id'
        ),
        'update' => array(
            'appkey'    => 'required|exists:applications,appkey',
            'id'        => 'required|existsinapp:missingchilds,id,Max\\Missingchild\\Models\\Missingchild'
        ),
        'delete' => array(
            'appkey'    => 'required|exists:applications,appkey',
            'id'        => 'required|exists:missingchilds,id'
        )
    );

    use \BaseModel;

    function __construct()
    {
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

    public function articles()
    {
        return $this->belongsToMany('Kitti\\Articles\\Article', 'article_missingchild');
    }

    public function app_content()
    {
        return $this->belongsTo('Kitti\\Articles\\Article', 'article_id');
    }


    public function gallery()
    {
        return $this->morphOne('Kitti\\Galleries\\Gallery', 'galleryable', 'content_type', 'content_id');
    }

    public function owner()
    {
        return $this->belongsTo('Max\\User\\Models\\User', 'user_id')->select(array('id','username'));
    }

    public function categories()
    {
        return $this->belongsToMany('Indianajone\\Categories\\Category', 'category_missingchild');
    }

    public function attachRelation($related, $category)
    {
        if(is_object($category))
            $category = $category->getKey();
        if(is_array($category))
            $category = $category['id'];

        $this->{$related}()->attach($category);
    }

    public function attachRelations($related, $categories)
    {
        if (is_string($categories))
            $categories = explode(',', $categories);

        // $categories = Category::findMany($categories);
        foreach ($categories as $category) {
            $this->attachRelation($related, $category);
        }
    }

    public function detachRelation($related, $category)
    {
        if(is_object($category))
            $category = $category->getKey();
        if(is_array($category))
            $category = $category['id'];

        $this->{$related}()->detach($category);
    }

    public function detachRelations($related, $categories)
    {
        if (is_string($categories))
            $categories = explode(',', $categories);

        foreach ($categories as $category) {
            $this->detachRelation($related, $category);
        }
    }

    public function syncRelations($related, $categories)
    {
        if (is_string($categories))
            $categories = explode(',', $categories);

        $this->{$related}()->sync($categories);
    }

    public function getReportedAtAttribute($value)
    {
        return $this->formatTime($value);
    }

    public function getMissingAtAttribute($value)
    {
        return $this->formatTime($value);
    }

    public function getCategoryIds()
    {
        return $this->types()->get(array('category_id'))->toArray();
    }

    public function hasCategory($id)
    {
        $cat = $this->types()->whereCategoryId($id)->first();
        return $cat ? true : false;
    }

    // public function newCollection(array $models = array())
    // {
    //     return new Collection($models);
    // }
}
