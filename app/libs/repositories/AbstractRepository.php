<?php 

use Illuminate\Support\MessageBag;
use Illuminate\Support\Contracts\ArrayableInterface;

abstract class AbstractRepository
{
	/**
   	 * @var Illuminate\Database\Eloquent\Model
   	 */
  	protected $model;

  	/**
   	 * @var Illuminate\Support\MessageBag
   	 */
  	protected $errors;

  	public function __construct($model)
  	{
  		$this->model = $model;
  	}

  	abstract function all();

  	/**
	 * Validate as defined rules in Model.
	 *
	 * @param 	string 	$action
	 * @param  	array 	$input
	 * @return 	string|boolean
	 *
	 */
	public function validate($action, $input=null)
	{
		$validator = Validator::make($input ?: Input::all(), $this->model->rules($action));

		if($validator->passes()) return true;

		$this->errors = $validator->messages()->first();
		
		return false;
	}

	public function find($id)
	{
		return $this->model->findOrFail($id)->fields();
	}

	public function create($input)
	{
		return $this->model->fill($input);
	}

	public function count()
	{
		$app_id = Appl::getAppIDByKey(Input::get('appkey', null));

		return $this->model->whereAppId($app_id)->count();
	}

	public function update($id, $input)
	{
		$model = $this->model->findOrFail($id);
		
		foreach( $model->getAttributes() as $key => $value )
		{
			if( array_key_exists($key, $input) && $value != $input[$key] )
			{
				if(array_key_exists('picture', $input))
	         {
	            $response = $model->createPicture($model->app_id);
	            if(is_object($response)) return $response;             	
	             unset($input['picture']);
				}
				else
				{
					$model->$key = $input[$key];
				}
			}
		}

		return $model;
	}

	public function delete($id)
	{
		return $this->model->findOrFail($id)->delete();
	}

	public function getModel() 
    {
        return new $this->model;
    }

	/**
   	 * Return the errors
   	 *
   	 * @return Illuminate\Support\MessageBag
     */
  	public function errors()
  	{
    	return $this->errors;
  	}

  	/**
  	 *	Maybe or not?
  	 */
	// public function __call($method, $args)
	// {
	// 	return call_user_func_array([$this->model, $method], $args);
	// }

}