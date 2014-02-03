<?php namespace Kitti\Medias;

class Media extends \BaseModel
{
        protected $table = 'medias';
        protected $hidden = array('app_id', 'gallery_id', 'status');

        public static $rules = array(
                'show' => array(
                        'appkey' => 'required|exists:applications'
                ),
                'show_with_id' => array(),
                'create' => array(
                        'appkey' => 'required|exists:applications',
                        'picture' => 'required',
                        'type' => 'required|in:image,video,audio',
                        'gallery_id' => 'required|exists:galleries,id'
                ),
                'update' => array(),
                'delete' => array()
        );

}