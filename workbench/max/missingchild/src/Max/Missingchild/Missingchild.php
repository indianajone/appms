<?php namespace Max\Missingchild\Models;

class Missingchild extends \BaseModel 
{
	protected $table = 'missingchilds';
    protected $guarded = array('id');
    protected $hidden = array('order', 'status', 'gallery_id');

	public static $rules = array(
        'show' => array(
            'appkey'    => 'required|exists:applications,appkey',
        ),
        'show_with_id' => array(
            'appkey'    => 'required|exists:applications,appkey',
            'id'        => 'required|exists:missingchilds,id'
        ),
        'create' => array(
        	'appkey'			=> 'required|exists:applications,appkey',
            'category_id'       => 'required|exists:categories,id',
            'title'             => 'required',
            'content'           => 'required',
        	'first_name'		=> 'required',
        	'last_name'			=> 'required',
        	'lost_age'			=> 'required|integer',
        	'place_of_missing' 	=> 'required',
        	'missing_date'		=> 'required',
        	'report_date'		=> 'required',
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


    public function type()
    {
        return $this->belongsToMany('Indianajone\\Categories\\Category', 'category_missingchild');
    }

    public function gallery()
    {
        return $this->hasOne('Kitti\\Galleries\\Gallery', 'content_id')->where('content_type', '=', 'child');
    }

    public function articles()
    {
        return $this->belongsToMany('Kitti\\Articles\\Article', 'article_missingchild');
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

    public function detachRelations($categories)
    {
        foreach ($categories as $category) {
            $this->detachRelation($category);
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
        return $this->type()->get(array('category_id'))->toArray();
    }

    public function hasCategory($id)
    {
        $cat = $this->type()->whereCategoryId($id)->first();
        return $cat ? true : false;
    }
}