<?php namespace Kitti\Medias;

class Media extends \BaseModel
{
        protected $table = 'medias';
        protected $guarded = array('id');
        protected $hidden = array('app_id', 'gallery_id', 'status');    
        
        public static $rules = array(
                'show' => array(
                        'id' => 'required|exists:medias'
                ),
                'create' => array(
                        // 'appkey' => 'required',
                        'gallery_id' => 'required',
                        'name' => 'required',
                        'data' => 'required',
                        'type' => 'required|in:image,video,audio'
                ),
                'update' => array(
                        'name' => 'required',
                        'type' => 'in:image,video,audio'
                ),
                'delete' => array(
                  'id' => 'required|exists:medias'
                )
        );

        public function gallery()
        {
                return $this->belongsTo('Gallery', 'gallery_id');
        }

        public function imageable()
        {
                return $this->morphTo();
        }

        public function scopeActive($query)
        {
                return $query->whereStatus(1);
        }

}