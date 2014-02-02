<?php namespace Kitti\Galleries;

class Galleries extends \BaseModel
{
	protected $table = 'galleries';
	// protected $fillable = array('app_id', 'gallery_id', 'categories_id','pre_title','picture','teaser','content','wrote_by','publish_at','views','tags','status');
	// protected $guarded = array('id');

	public function like() {
		//$this->setVisible(array('id'));
		return $this->hasMany('Kitti\Medias\Likes','content_id','id')->join('members', 'likes.member_id', '=', 'members.id')->setVisible(array('id'));
	}
}