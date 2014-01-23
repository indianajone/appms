<?php namespace Indianajone\Validators\Rules;
	
class ExistsOrNull extends \Illuminate\Validation\Validator
{	
	public function validateExistsOrNull($attribute, $value, $parameters)
    {
    	if((int)$value !== 0) 
    		return $this->validateExists($attribute, $value, $parameters);
    	
    	return true;
    }
}