<?php namespace Kitti\Galleries;

class Gallery extends \BaseModel
{
    protected $table = 'galleries';
  	protected $guarded = array('id');
    protected $hidden = array('app_id', 'status');

    public static $rules = array(
    	'show' => array(
    		'id' => 'required|exists:galleries'
    	),
    	'show_by_owner' => array(
    		'type' => 'required|in:member,article',
    		'id' => 'required',
    	),
    	'create' => array(
    		// 'appkey' => 'required',
    		'content_id' => 'required',
    		'content_type' => 'required|in:member,article',
    		'name' => 'required'
    	)
    );

    public function owner()
    {
    	switch($this->attribute['type'])
    	{
    		default: //case 'member':
    			$model = 'Max\\Member\\Models\\Member';
    		break;
    	}

    	return $this->belongsTo($model, 'content_id');
    }

    public function media()
    {
    	return $this->morphMany('Media', 'imageable');
    }

    public function medias()
    {
    	return $this->hasMany('Kitti\\Medias\\Media', 'gallery_id');
    }

    public function scopeActive($query)
    {
        return $query->whereStatus(1);
    }

    public function scopeOwner($query, $type, $id)
    {
    	return $query->whereType($type)->whereContentId($id);
    }
}