<?php namespace Indianajone\Validators\Rules;
	
use Appl, Input;

trait UniqueInApp 
{	
	public function validateUniqueInApp($attribute, $value, $parameters)
    {
    	$parameters[3] = 'app_id';
		$parameters[4] = Appl::getAppIDByKey(Input::get('appkey')); 
    	
    	return $this->validateUnique($attribute, $value, $parameters);
    }
}