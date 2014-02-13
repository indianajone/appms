<?php

use Carbon\Carbon;

class BaseModel extends Eloquent 
{

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
        'search' => null,
        'time' => null
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
                // var_dump($field);
                $query = $query->$filter($input);
            }
        }

        return $query;
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
            $query->whereHas('types', function($type) use($ids){
                $type->whereIn('category_id', $ids);
            });
        }

        $query = $query->with('types');

        return $query;
    }

    public function scopeOrder_by($query, $order)
    {
        $field = $order[0];
        $dir = $order[1];
        
        if(\Schema::hasColumn($this->getTable(), $field))
           return $query->orderBy($field, $dir);
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
	    		$time = Carbon::createFromFormat($format, $value, \Config::get('app.timezone'));
	    		return $query->where($field, '>=', $time->timestamp);
	    	}
	    	catch(exception $e)
	    	{
	    		return $query->where($field, '>=', strtotime($value));
	    	}
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