<?php namespace Indianajone\Validators\Rules;

trait ExistsOrNull
{	
	public function validateExistsOrNull($attribute, $value, $parameters)
    {
    	if((int)$value !== 0) 
    		return $this->validateExists($attribute, $value, $parameters);
    	
    	return true;
    }
}