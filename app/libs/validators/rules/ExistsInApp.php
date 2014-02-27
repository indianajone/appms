<?php namespace Indianajone\Validators\Rules;

use Appl, Input, Validator;

trait ExistsInApp
{	
    public function validateExistsInApp($attribute, $value, $parameters)
    {
		$table = $parameters[0];
		$column = $parameters[1];
		$model = array_key_exists(2, $parameters) ? $parameters[2] : 'Max\\User\\Models\\User';
		$app_id = array_key_exists(3, $parameters) ? $parameters[3] : Appl::getAppIDByKey(Input::get('appkey'));

		$verifier =  $this->getPresenceVerifier();

		if($verifier->getCount($table, $column, $value) >= 1)
		{
				$data = $model::find($value);
				if($data) return $data->exists;

			return false;
		}
		
		return false;
    }
}