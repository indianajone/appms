<?php namespace Indianajone\Validators\Rules;

use Max\User\Models\User;
use Indianajone\Applications\Application as Appl;
	
class ExistsInApp extends \Illuminate\Validation\Validator
{	
	public function validateExistsInApp($attribute, $value, $parameters)
    {
    	$app_id = $parameters[2] ?: \Appl::getAppIDByKey(\Input::get('appkey'));
		$id = $value;

		$table = $parameters[0];
		$column = $parameters[1];

		$verifier = $this->getPresenceVerifier();

		if($verifier->getCount($table, $column, $id) >= 1)
		{
			$app = Appl::find($app_id)->owner()->whereId($id)->first();
			if(is_null($app))
			{
				$user = User::find($id);
				if($user->parent_id == 0 || is_null($user->parent_id))
				{
					return false;
				}
				
				$parameters[2] = $app_id;
				return $this->validateExistsInApp($attribute, $user->getKey(), $parameters);
				
			}	

			return $app->exists;	
		}
		return false;
    }
}