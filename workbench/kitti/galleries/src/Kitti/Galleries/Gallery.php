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
    	),
        'delete' => array(
          'id' => 'required|exists:galleries'
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

    // public function like() 
    // {
    //     //return $this->hasMany('Kitti\\Articles\\Like', 'content_id');
    //     return $this->hasMany('Kitti\\Articles\\Like','content_id','id')
    //             ->join('members', 'likes.member_id', '=', 'members.id');
    // }

    public function scopeActive($query)
    {
        return $query->whereStatus(1);
    }

    public function scopeOwner($query, $type, $id)
    {
    	return $query->whereType($type)->whereContentId($id);
    }

    // public function like()
    // {
    //     //return $this->belongsTo('Kitti\\Articles\\Like', 'id','content_id');

    // }

    // public function scopeLike($query, $user_id)
    // {
    //     return $query->leftJoin('content_userdata', function($join) use ($user_id)
    //     {
    //         $join->on('content_userdata_content_id', '=', 'content.content_id')
    //              ->on('content_userdata_user_id',    '=', DB::raw($user_id));
    //     });
    // }
}