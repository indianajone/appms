<?php namespace Max\Missingchild\Models;

use Input;
use Response;

class Missingchild extends \BaseModel 
{
	protected $table = 'missingchilds';
    protected $guarded = array('id');
    protected $hidden = array('status', 'app_id', 'gallery_id', 'article_id', 'user_id', 'types', 'title', 'categories');

    /*
     * The following $map array maps the url query string to
     * the corresponding model filter e.g.
     *  ->order_by will handle Input::get('order_by')
     */
    protected $map = array(
        'order_by' => 'order_by',
        'limit' => 'limit',
        'offset' => 'offset',
        'search' => 'q',
        'filterCats' => 'category_id',
        'whereUpdated' => 'updated_at',
        'whereCreated' => 'created_at'
    );

    /*
     *  Default values for the url parameters
     */
    protected $defaults = array(
        'order_by' => null,
        'limit' => 10,
        'offset' => 0,
        'search' => null,
        'filterCats' => '*',
        'time' => null
    );

    /*
     * The following filters are defined by
     *  url parameters can have multiple
     *  values separated by a delimiter
     *  e.g. order_by, sort
     */
    protected $multiple = array(
        'filterCats',
        'order_by'
    );

	public static $rules = array(
        'show' => array(
            'appkey'    => 'required|exists:applications,appkey'
        ),
        'show_with_id' => array(
            'appkey'    => 'required|exists:applications,appkey',
            'id'        => 'required|exists:missingchilds,id'
        ),
        'create' => array(
        	'appkey'			=> 'required|exists:applications,appkey',
            'category_id'       => 'required|existsloop:categories,id',
            // 'article_type'      => 'required|exists:categories,id',
            'title'             => 'required',
            'content'           => 'required',
        	'first_name'		=> 'required',
        	'last_name'			=> 'required',
        	'lost_age'			=> 'required|integer',
        	'place_of_missing' 	=> 'required',
        	'missing_at'		=> 'required',
        	'report_at'		=> 'required',
        	'user_id'			=> 'exists:users,id',
        	'order'				=> 'integer'
        ),
        'create_clue' => array(
            'appkey'         => 'required|exists:applications,appkey',
            'id'             => 'required|exists:missingchilds,id',
            'article_id'     => 'required|exists:articles,id'
        ),
        'update' => array(
            'appkey'    => 'required|exists:applications,appkey',
            'id'        => 'required|exists:missingchilds,id'
        ),
        'delete' => array(
            'appkey'    => 'required|exists:applications,appkey',
            'id'        => 'required|exists:missingchilds,id'
        )
    );

    public function articles()
    {
        return $this->belongsToMany('Kitti\\Articles\\Article', 'article_missingchild');
    }

     public function app_content()
    {
        // return $this->articles()
        // ->whereHas('categories', function($q){
        //     $q->where('name', '=', 'child_detail');
        // });

        return $this->hasOne('Kitti\\Articles\\Article', 'id', 'article_id');
    }

    public function gallery()
    {
        // return $this->hasOne('Kitti\\Galleries\\Gallery', 'content_id')->where('content_type', '=', 'child');
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
        foreach ($categories as $category) {
            $this->detachRelation($related, $category);
        }
    }

    public function syncRelations($related, $categories)
    {
        $this->{$related}()->sync($categories);
    }

    public function getReportDateAttribute($value)
    {
        return $this->formatTime($value);
    }

    public function getMissingDateAttribute($value)
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
}