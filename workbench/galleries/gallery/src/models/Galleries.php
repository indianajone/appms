<?php namespace Galleries\Gallery\Models;

class Galleries extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'galleries';
    protected $fillable = array('app_id', 'content_id', 'type','name','description','picture','like','publish_date','status');
    protected $primaryKey = "gallery_id";
}
