<?php namespace Indianajone\Categories\Repositories;

use Appl, Input;
use Indianajone\Categories\Category;

class DBCategoryRepository extends \AbstractRepository implements CategoryRepositoryInterface
{
	/**
	 * Required columns for hierarchy sorting.
	 *
	 * @var Array
	 */
	protected $columns = array('id', 'name', 'lft', 'rgt', 'parent_id', 'depth');

	public function __construct(Category $cat)
	{
		parent::__construct($cat);
	}

	public function all()
	{
		$cats = $this->model->whereAppId(Appl::getAppIDByKey(Input::get('appkey')))->apiFilter()->get();

		foreach ($cats as $key => $cat) {
			$cat->fields();
			if($cat->isRoot()) 
			{
				$cat->setRelation('children', $cat->getDescendants($this->columns)->toHierarchy());
			}
		}	
		return $cats;
	}

	public function find($id)
	{
		$cat = $this->model->whereId($id)->whereAppId(Appl::getAppIDByKey(Input::get('appkey')))->apiFilter()->first();
		if( is_null($cat) ) return $cat;
		if( !$cat->isLeaf() ) $cat->setRelation('children', $cat->getDescendants($this->columns)->toHierarchy());
		return $cat->fields()->toArray();
	}
}