<?php
namespace Max\Missingchild\Models;

class ArticleMissingchild extends \Eloquent {
 
    protected $table = 'article_missingchild';
    
    public function article(){
        return $this->hasOne('Max\Missingchild\Models\Article', 'id', 'article_id');
    }
    
    public function missingchild(){
        return $this->hasOne('Max\Missingchild\Models\Missingchild', 'id', 'missingchild_id');
    }
}
?>