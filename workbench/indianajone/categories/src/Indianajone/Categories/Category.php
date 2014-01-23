<?php namespace Indianajone\Categories;
use Baum\Node;

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

  /** 
   * Override getDateFormat to unixtime stamp.
   * @return String
   */
  protected function getDateFormat()
  {
      return 'U';
  }

  public static $rules = array(
    'save' => array(
      'appkey' => 'required|exists:applications,appkey',
      'name' => 'required',
      // 'app_id' => 'required|exists:applications,id',
      'parent_id' => 'exists:categories,id'
    ),
    'update' => array(
      'parent_id' => 'integer|existsornull:categories,id'
    ),
    'delete' => array(
      'id' => 'required|exists:categories'
    )
  );

  public static $messages = array(
    'exists' => 'The given :attribute is invalid.'    
  );

  //////////////////////////////////////////////////////////////////////////////

  //
  // Below come the default values for Baum's own Nested Set implementation
  // column names.
  //
  // You may uncomment and modify the following fields at your own will, provided
  // they match *exactly* those provided in the migration.
  //
  // If you don't plan on modifying any of these you can safely remove them.
  //

  // /**
  // * Column name which stores reference to parent's node.
  // *
  // * @var int
  // */
  // protected $parentColumn = 'parent_id';

  // /**
  // * Column name for the left index.
  // *
  // * @var int
  // */
  // protected $leftColumn = 'lft';

  // /**
  // * Column name for the right index.
  // *
  // * @var int
  // */
  // protected $rightColumn = 'rgt';

  // /**
  // * Column name for the depth field.
  // *
  // * @var int
  // */
  // protected $depthColumn = 'depth';

  // /**
  // * With Baum, all NestedSet-related fields are guarded from mass-assignment
  // * by default.
  // *
  // * @var array
  // */
  // protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

  //
  // This is to support "scoping" which may allow to have multiple nested
  // set trees in the same database table.
  //
  // You should provide here the column names which should restrict Nested
  // Set queries. f.ex: company_id, etc.
  //

  /**
   * Columns which restrict what we consider our Nested Set list
   *
   * @var array
   */
  protected $scoped = array('app_id');

  //////////////////////////////////////////////////////////////////////////////

  //
  // Baum makes available two model events to application developers:
  //
  // 1. `moving`: fired *before* the a node movement operation is performed.
  //
  // 2. `moved`: fired *after* a node movement operation has been performed.
  //
  // In the same way as Eloquent's model events, returning false from the
  // `moving` event handler will halt the operation.
  //
  // Below is a sample `boot` method just for convenience, as an example of how
  // one should hook into those events. This is the *recommended* way to hook
  // into model events, as stated in the documentation. Please refer to the
  // Laravel documentation for details.
  //
  // If you don't plan on using model events in your program you can safely
  // remove all the commented code below.
  //

  // /**
  //  * The "booting" method of the model.
  //  *
  //  * @return void
  //  */
  // protected static function boot() {
  //   // Do not forget this!
  //   parent::boot();

  //   static::moving(function($node) {
  //     // YOUR CODE HERE
  //   });

  //   static::moved(function($node) {
  //     // YOUR CODE HERE
  //   });
  // }

  public function updateParent($id)
  {
    if($id >= 1)
      $this->makeChildOf($id);
    else
      $this->makeRoot();
  }

  public function getChildrenAttribute($value)
  {
    return $value ?: null;
  }

  public function setChildrenAttribute($value)
  {
    dd($value);
  }

  public function addHidden($attribute)
{
    $hidden = $this->getHidden();

    array_push($hidden, $attribute);

    $this->setHidden($hidden);

    // Make method chainable
    return $this;
}

/**
 * Convert appended collections into a list of attributes
 *
 * @param  object       $data       Model OR Collection
 * @param  string|array $levels     Levels to iterate over
 * @param  string       $attribute  The attribute we want to get listified
 * @param  boolean      $hideOrigin Hide the original relationship data from the result set
 * @return Model
 */
public function listAttributes($data, $levels, $attribute = 'id', $hideOrigin = true)
{

    // Set some defaults on first call of this function (because this function is recursive)
    if (! is_array($levels))
        $levels = explode('.', $levels);

    if ($data instanceof Illuminate\Database\Eloquent\Collection) // Collection of Model objects
    {
        // We are dealing with an array here, so iterate over its contents and use recursion to look deeper:
        foreach ($data as $row)
        {
            $this->listAttributes($row, $levels, $attribute, $hideOrigin);
        }
    }
    else
    {
        // Fetch the name of the current level we are looking at
        $curLevel = array_shift($levels);

        if (is_object($data->{$curLevel}))
        {
            if (! empty($levels))
            {
                // We are traversing the right section, but are not at the level of the list yet... Let's use recursion to look deeper:
                $this->listAttributes($data->{$curLevel}, $levels, $attribute, $hideOrigin);
            }
            else
            {
                // Hide the appended collection itself from the result set, if the user didn't request it
                if ($hideOrigin)
                    $data->addHidden($curLevel);

                // Convert Collection to Eloquent lists()
                if (is_array($attribute)) // Use specific attributes as key and value
                    $data->{$curLevel . '_' . $attribute[0]} = $data->{$curLevel}->lists($attribute[0], $attribute[1]);
                else // Use specific attribute as value (= numeric keys)
                    $data->{$curLevel . '_' . $attribute} = $data->{$curLevel}->lists($attribute);
            }
        }
    }

    return $data ?: null;
}

}
