<?php namespace Indianajone\Validators\Rules;
	
class ExistLoop extends \Illuminate\Validation\Validator
{	
	public function validateExistLoop($attribute, $value, $parameters)
    {
    	// Duplicate from Existing exist rule.
    	$values = explode(',', $value);
		foreach ($values as $i => $value) 
		{
			$results[] = $this->validateExists($i, $value, $parameters);
			if(!$results[$i])
			{
				$this->setCustomMessages(array('existloop' => 'permission_id: '.$value.' does not exists.'));
				return false;
			} 
		}

		return true;
    }
}