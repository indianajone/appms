<?php namespace Indianajone\Categories;

use Appl, Cache, Input;
use Baum\Node;
use Carbon\Carbon;
use Indianajone\Categories\Extensions\Eloquent\Collection;

/**
* MODEL
*/
class Category extends Node {

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'categories';

  protected $rules = array(
    'show' => array(
      'appkey' => 'required|exists:applications,appkey',
    ),
    'create' => array(
      'appkey' => 'required|exists:applications,appkey',
      'name' => 'required',
      'parent_id' => 'exists:categories,id'
    ),
    'update' => array(
      'appkey' => 'required|exists:applications,appkey',
      'parent_id' => 'integer|existsOrNull:categories,id'
    ),
    'delete' => array(
      'appkey' => 'required|exists:applications,appkey',
      'id' => 'required|exists:categories'
    )
  );

  protected $hidden = array('pivot', 'app_id', 'parent_id', 'lft', 'rgt', 'depth');

  use \BaseModel;

  // /**
  // * With Baum, all NestedSet-related fields are guarded from mass-assignment
  // * by default.
  // *
  // * @var array
  // */
  protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

  /**
   * Columns which restrict what we consider our Nested Set list
   *
   * @var array
   */
  protected $scoped = array('app_id');

  protected static function boot() 
  {
    static::deleting(function($cat) 
    {  
      $cat->articles->each(function($article) use($cat)
      {
          $article->detachCategory($cat->getKey());
      });

      $cat->missingchild->each(function($child) use($cat)
      {
        $child->detachRelation('categories' ,$cat->getKey());
      });
    });

    parent::boot();
  }

  public function scopeSearch($query)
  {
      return $this->keywords(array('name'));
  }

  public function updateParent($id)
  {
    if($id >= 1)
      $this->makeChildOf($id);
    else
      $this->makeRoot();
  }

  public function newCollection(array $models = array())
  {
    return new Collection($models);
  }

  public function articles()
  {
    return $this->belongsToMany('Kitti\\Articles\\Article', 'article_category');
  }

  public function missingchild()
  {
    return $this->belongsToMany('Max\\Missingchild\\Models\\Missingchild', 'category_missingchild');
  }

}
