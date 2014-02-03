<?php namespace Indianajone\Validators\Rules;
	
class ExistLoop extends \Illuminate\Validation\Validator
{	
	public function validateExistLoop($attribute, $value, $parameters)
    {
    	// Duplicate from Existing exist rule.
    	$values = explode(',', $value);
		foreach ($values as $i => $value) 
		{
			$parameters[3] = 'app_id';
			$parameters[4] = \Appl::getAppIDByKey(\Input::get('appkey')); 
			$results[] = $this->validateExists($i, $value, $parameters);
			if(!$results[$i])
			{
				$this->setCustomMessages(array('existloop' => $attribute.': '.$value.' does not exists.'));
				return false;
			} 
		}

		return true;
    }
}