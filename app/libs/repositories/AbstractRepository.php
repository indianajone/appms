<?php 

use Illuminate\Support\MessageBag;

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
		return $this->model->apiFilter()->findOrFail($id)->fields();
	}

	public function create($input)
	{
		return $this->model->create($input);
	}

	public function update($id, $input)
	{
		return $this->model->findOrFail($id)->update($input);
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
}