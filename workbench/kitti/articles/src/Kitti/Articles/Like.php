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
		return $this->hasOne('Kitti\\Medias\\Members', 'id' , 'member_id');
	}

	public function scopeDeleteArticle($query , $param) {
		return $query->where('content_id','=', $param['id'])
				->where('member_id','=', $param['member_id'])
					->where('type','=','article')->delete();
	}

	public function scopeDeleteLike($query , $id , $member_id, $type) {
		return $query->where('content_id','=', $id)
				->where('member_id','=', $member_id)
					->where('type','=',$type)->delete();
	}

	public function scopeListByType($query , $id , $type) {
		return $query->where('content_id','=', $id)->where('likes.type','=', $type)
            ->join('members', 'likes.member_id', '=', 'members.id')
            ->select('members.id', 'members.parent_id','username' ,'first_name','last_name',
                'gender','email','phone','mobile','verified','fbid','fbtoken','birthday',
                'members.type','members.created_at','members.updated_at');
	}

	public function scopeListByTypeMini($query , $id , $type) {
		return $query->where('content_id','=', $id)->where('likes.type','=', $type)
            ->join('members', 'likes.member_id', '=', 'members.id')
            ->select('members.id','first_name','last_name', 'username');
	}
}