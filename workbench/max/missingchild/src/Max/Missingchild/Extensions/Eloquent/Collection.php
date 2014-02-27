<?php namespace Max\Missingchild;

use Indianajone\Categories\Extensions\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
	public function toCategories($colums=array())
	{
		return new BaseCollection($this->categories($this->items));
	}

	protected function categories(&$result)
	{

		// dd(func_num_args());
		$return = array();
		
		if ( is_array($result) ) {
			while( list($n, $sub) = each($result) ) {
				$types = $sub->categories()->withPivot('id')->get(array('categories.id', 'name'));

				// foreach ($types as $type) 
				// {
				// 	$return[] = $type->get()->toHierarchy();
				// }
				dd(\DB::getQueryLog());
				var_dump($types->toJson());
			}
		}

		return $return;
	}
}

/*
 foreach ($children as $item => $child) {
            	$child->fields();
            	$types = $child->categories()->get(); //->remember(1)
            	$obj = [];
            	foreach ($types as $type) {
            		if(!$type->isRoot())
            		{
            			$name = Category::whereId($type->getParentId())->first()->name; //->remember(1)
            			if(!array_key_exists($name,$obj)) 
            				$obj[$name] = [];

            			array_push($obj[$name], $type->toArray());
            		}

            		foreach ($obj as $key => $type) {
            			$child->setRelation($key, new Collection($type));
            		}
            	}
            }
*/

