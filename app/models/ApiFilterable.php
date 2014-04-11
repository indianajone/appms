<?php

Trait ApiFilterable
{
	/*
     * The following $map array maps the url query string to
     * the corresponding model filter e.g.
     *  ->order_by will handle Input::get('order_by')
     */
    protected $map = array(
        'search' => 'q',
        'whereUpdated' => 'updated_at',
        'whereCreated' => 'created_at',
        'limit' => 'limit',
        'offset' => 'offset',
        'order_by' => 'order_by'
    );

    /*
     *  Default values for the url parameters
     */
    protected $defaults = array(
        'order_by' => null,
        'limit' => 10,
        'offset' => 0,
        // 'search' => null
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

	public function getMap()
    {
        return $this->map;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function getMultiple()
    {
        return $this->multiple;
    }

    public function setMap($value)
    {
        $this->map = $value;
    }

    public function setDefaults($value)
    {
        $this->defaults = $value;
    }

    public function setDelimiter($value)
    {
        $this->delimiter = $value;
    }

    public function setMultiple($value)
    {
        $this->multiple = $value;
    }
}