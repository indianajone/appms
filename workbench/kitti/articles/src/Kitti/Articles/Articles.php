<?php namespace Kitti\Articles;

class Articles extends \BaseModel
{
	protected $table = 'articles';
	protected $fillable = array('app_id', 'gallery_id', 'categories_id','pre_title','picture','teaser','content','wrote_by','publish_at','views','tags','status');
	protected $guarded = array('id');
}