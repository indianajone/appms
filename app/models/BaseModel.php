<?php

use Carbon\Carbon;
use \Image, \Config;
use Indianajone\Categories\Category;

class BaseModel extends Eloquent 
{
    protected $hidden = array();
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
        'whereUpdated' => 'updated_at',
        'whereCreated' => 'created_at'
    );

    /*
     *  Default values for the url parameters
     */
    protected  $defaults = array(
        'order_by' => null,
        'limit' => 10,
        'offset' => 0,
        'search' => null
    );

    /*
     * The following filters are defined by
     *  url parameters can have multiple
     *  values separated by a delimiter
     *  e.g. order_by, sort
     */
    protected  $multiple = array(
        'order_by'
    );

    /*
     * Delimiter that separates multiple url parameter values
     *  e.g. ?category_id=1,2
     */
    protected  $delimiter = ',';

	/** 
	 * Override getDateFormat to unixtime stamp.
	 * @return String
	 */
	protected function getDateFormat()
    {
        return 'U';
    }

    public function formatTime($value)
    {
    	$format = \Input::get('date_format', null);
		return $format ? Carbon::createFromTimeStamp($value, \Config::get('app.timezone'))->format($format) : $value;    
    }

    public function getCreatedAtAttribute($value)
	{
		return $this->formatTime($value);
	}

	public function getUpdatedAtAttribute($value)
	{
		return $this->formatTime($value);    
	}

	public function getPublishAtAttribute($value)
	{
		return $this->formatTime($value); 
	}

	public function scopeKeywords($query, $fields)
	{
		$keyword = \Input::get('q');
		$builder =  $query->where($fields[0],'like', '%'.$keyword.'%');

		foreach ($fields as $key => $field) {
			if($key >= 1) $builder = $builder->orWhere($field, 'like', '%'.$keyword.'%');
		}

		return $builder;
	}

	public function scopeApiFilter($query)
    {
        foreach ($this->map as $filter => $field) 
        {
            if (in_array($filter, $this->multiple)) 
            {
                $input = Input::get($field)
                    ? explode($this->delimiter, Input::get($field))
                    : array_get($this->defaults, $filter, null);
            } 
            else 
            {
                $input = Input::get($field)
                    ? Input::get($field)
                    : array_get($this->defaults, $filter, null);
            }

            if (!is_null($input)) 
            {
                $query = $query->$filter($input);
            }
        }

        return $query;
    }

    public function createPicture($app_id)
    {
        $picture = Input::get('picture', null);
        if($picture)
        {
            if(filter_var($picture, FILTER_VALIDATE_URL))
            {
                $this->update(array(
                    'picture' => $picture
                ));
            }
            else
            {
                $response = Image::upload($picture);

                if(is_object($response)) return $response;

                if($this->gallery)
                {
                    $this->gallery->medias()->create(array(
                         'app_id' => $app_id,
                         'gallery_id' => $this->gallery->id,
                         'name' => 'Image',
                         'picture' => $response,
                         'type' => 'image'
                    ));
                }

                $this->update(array(
                    'picture' => $response
                ));
            }
        }
    }

    public function fields()
    {
        $field = Input::get('fields', null);
        $fields = $field ? explode(',', $field) : $field;

        $hidden = Input::get('hidden', null);
        $hiddens = $hidden ? explode(',', $hidden) : $hidden;
        
        if($fields) $this->setVisible($fields);
        if($hiddens) $this->setHidden($hiddens);
    }

    public function scopeFilterCats($query, $ids)
    {
        if($ids != '*')
        {
            $categories = Category::findMany($ids);
            
            foreach($categories as $category)
            {
                $cids = $category->getDescendantsAndSelf(['id'])->lists('id'); 
                $query = $query->whereIn('id', function($type) use($cids) {

                    $foreign_key = $this->getForeignKey();
                    $other_key = $this->categories()->getOtherKey();
                    $pivot = $this->categories()->getTable();

                    $type->select($foreign_key)->from($pivot)->whereIn($other_key, $cids)->groupBy($foreign_key);
                });
            }
        }

        return $query;
    }

    public function scopeOrder_by($query, $order)
    {
        if(!array_key_exists(1, $order)) $order[1] = 'asc';

        $field = $order[0];
        $dir = $order[1];

        
        if(\Schema::hasColumn($this->getTable(), $field))
        {
            return $query->orderBy($field, $dir);
        }   
    }

    public function scopeSearch($query)
    {
        return $this->keywords(array('first_name', 'last_name'));
    }

    public function scopeTime($query, $field, $value)
	{
        $format = \Input::get('date_format', null);

	    if($format)
	    {
	    	try {
                $time = Carbon::createFromFormat($format, $value, Config::get('app.timezone'));
            }
            catch(InvalidArgumentException $e)
            {
                throw new InvalidArgumentException('date format '. $format . ' and '. $value .' are not match');
            }

	    	if($time) return $query->where($field, '>=', $time->timestamp);
	    }
	    
	    return $query->where($field, '>=', $value);
	}

    public function scopeWhereUpdated($query, $time)
    {
        return $this->time('updated_at', $time);
    }

     public function scopeWhereCreated($query, $time)
    {
        return $this->time('created_at', $time);
    }

	public function scopeApp($query)
    {
        return $query->whereAppId(Appl::getAppIdByKey(Input::get('appkey')));
    }

    public function scopeActive($query)
    {
        return $query->whereStatus(1);
    }
}