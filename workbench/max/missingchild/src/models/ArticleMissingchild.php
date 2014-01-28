<?php
namespace Max\Missingchild\Models;

class ArticleMissingchild extends \Eloquent {
 
    protected $table = 'article_missingchild';
    
    public static $rules = array(
        'create' => array(
            'user_id' => 'required',
            'title'  => 'required',
            'content' => 'required',         
        ),
        'show' => array(
            'id' => 'required|exists:article_missingchild'
        ),
        'update' => array(
            'id' => 'required|exists:article_missingchild'
        ),
        'delete' => array(
            'id' => 'required|exists:article_missingchild'
        ),
    );
    
    protected function getDateFormat()
    {
        return 'U';
    }
    
    public function article(){
        return $this->hasOne('Max\Missingchild\Models\Article', 'id', 'article_id');
    }
    
    public function missingchild(){
        return $this->hasOne('Max\Missingchild\Models\Missingchild', 'id', 'missingchild_id');
    }
}
?>