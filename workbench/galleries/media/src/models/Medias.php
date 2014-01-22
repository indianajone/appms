<?php namespace Galleries\Media\Models;

class Medias extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'medias';
    protected $fillable = array('app_id', 'member_id', 'gallery_id','name','description','path','filename','type','latitude','longitude','like','status');
    protected $primaryKey = "media_id";
}