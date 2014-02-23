<?php namespace Kitti\Galleries;

class Gallery extends \BaseModel
{
    protected $table = 'galleries';
  	protected $guarded = array('id');
    protected $hidden = array('app_id', 'status', 'content_type', 'content_id');

    public static $rules = array(
    	'show' => array(
    		'appkey' => 'required|exists:applications,appkey',
    	),
        'show_with_id' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id' => 'required|exists:galleries'
        ),
    	'show_by_owner' => array(
            'appkey' => 'required|exists:applications,appkey',
            /**
            #TODO Move Type Somewhere
            **/
    		'type' => 'required|in:member,article,child',
    		'id' => 'required|exists:galleries'
    	),
    	'create' => array(
    		'appkey' => 'required|exists:applications,appkey',
    		'content_id' => 'required',
             /**
            #TODO Move Type Somewhere
            **/
    		'content_type' => 'required|in:member,article,child',
    		'name' => 'required'
    	),
        'update' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id' => 'required|exists:galleries'
        ),
        'delete' => array(
            'appkey' => 'required|exists:applications,appkey',
            'id' => 'required|exists:galleries'
        )
    );

    public function owner()
    {
    	switch($this->attribute['content_type'])
    	{
            case 'article':
                $model = 'Kitti\\Articles\\Article';
            break;
    		default: //case 'member':
    			$model = 'Max\\Member\\Models\\Member';
    		break;
    	}

    	return $this->belongsTo($model, 'id');
    }

    public function medias()
    {
        $media = $this->hasMany('Kitti\\Medias\\Media', 'gallery_id');
        $media->app();
        if($media->getResults()->isEmpty())
            return $this->hasOne('Kitti\\Medias\\Media')->app();
        else
            return $media;
    }

    public function scopeOwner($query, $type, $id)
    {
    	return $query->whereContentType($type)->whereContentId($id);
    }

    public function galleryable()
    {
        return $this->morphTo();
    }
}