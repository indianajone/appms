<?php namespace Kitti\Medias;

class Medias extends \BaseModel
{
	protected $table = 'medias';
	// protected $fillable = array('app_id', 'gallery_id', 'categories_id','pre_title','picture','teaser','content','wrote_by','publish_at','views','tags','status');
	// protected $guarded = array('id');
	// public function scopeLike($query, $id) {
	// 	return $query->where('content_id','=', $id)->where('type','=','media')
	// }

	public function like() {
		return $this->hasMany('Kitti\Medias\Likes','content_id','id')
		->leftJoin('members', 'likes.member_id', '=', 'members.id');
	}
}