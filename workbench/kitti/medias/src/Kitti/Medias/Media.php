<?php namespace Kitti\Medias;

class Media extends \BaseModel
{
    protected $table = 'medias';
    protected $guarded = array('id');
    protected $hidden = array('app_id', 'gallery_id', 'status');

    protected $map = array(
        'order_by' => 'order_by',
        'limit' => 'limit',
        'offset' => 'offset',
        'whereUpdated' => 'updated_at',
        'whereCreated' => 'created_at'
   );

    public static $rules = array(
        'show' => array(
            'appkey' => 'required|exists:applications'
        ),
        'show_with_id' => array(
            'appkey' => 'required|exists:applications',
            'id' => 'required|exists:medias'
        ),
        'create' => array(
            'appkey' => 'required|exists:applications',
            'picture' => 'required',
            'type' => 'required|in:image,video,audio',
            'gallery_id' => 'required|exists:galleries,id'
        ),
        'update' => array(),
        'delete' => array(
            'id' => 'required|existsinapp:medias,id'
        )
    );

    public function galleries()
    {
        return $this->belongsToMany('Kitti\\Galleries\\Gallery');
    }
}