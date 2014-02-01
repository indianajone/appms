<?php namespace Kitti\Articles;

class Like extends \BaseModel
{
	protected $table = 'likes';
	protected $guarded = array('id');
	// protected $fillable = array('app_id', 'gallery_id', 'categories_id','pre_title','picture','teaser','content','wrote_by','publish_at','views','tags','status');
	// protected $guarded = array('id');

	public static $rules = array(
    	'like' => array(
    		'member_id' => 'required'
    	)
    );

	public function member() {
		return $this->hasOne('\Kitti\Medias\Members', 'id' , 'member_id');
	}

	public function scopeDeleteArticle($query , $param) {
		return $query->where('content_id','=', $param['id'])
				->where('member_id','=', $param['member_id'])
					->where('type','=','article')->delete();
	}
}