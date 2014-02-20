<?php namespace Indianajone\Validators\Rules;
	
use Appl, Input, Schema;

trait ExistsLoop 
{	
	public function validateExistsLoop($attribute, $value, $parameters)
    {
    	// Duplicate from Existing exist rule.
    	$values = explode(',', $value);
		foreach ($values as $i => $value) 
		{
			if(Schema::hasColumn($parameters[0], 'app_id'))
			{
				$parameters[3] = 'app_id';
				$parameters[4] = Appl::getAppIDByKey(Input::get('appkey')); 
			}
			$results[] = $this->validateExists($i, $value, $parameters);
			if(!$results[$i])
			{
				$this->setCustomMessages(array('existsloop' => $attribute.': '.$value.' does not exists.'));
				return false;
			} 
		}

		return true;
    }
}