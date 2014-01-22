<?php namespace Articles\Article\Models;

class Articles extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'articles';
        protected $primaryKey = "article_id";
        //protected $fillable = array('app_id', 'gallery_id', 'categories_id','pre_title','picture','teaser','content','wrote_by','publis_at','views','tags','status');
        //protected $guarded = array('format','article_id');
}